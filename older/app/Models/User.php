<?php

namespace App\Models;

use App\Helpers\PlanPerks;
use App\Helpers\ModelHelper;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;
use App\Helpers\ModelHelperConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, ModelHelper;

    public function modelHelperConfig(): ModelHelperConfig
    {
        return ModelHelperConfig::create()
            ->generate('username')->from($this->email)
            ->generateUuid();
       
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'avatar',
        'password',
        'role',
        'meta',
        'status',

        'dob_day',
        'dob_month',
        'dob_year',

        'country_code',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'address_line',
        'balance',
        'provider_id',
        'provider',

        'kyc_verified_at',
        'email_verified_at',
        'phone_verified_at',
        'card_user_id',
        'card_user_status',
        'acceptterms',
        'fund_source',
        'employment_status',
        'ssn_ein',
        'partner_id',
        'refer_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'status' => 'boolean',
        'is_star' => 'boolean',
        'meta' => 'array',
        'plan_data' => 'array',
    ];


    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name'),
        );
    }

    protected $appends = ['created_at_date', 'name'];

    public function getCreatedAtDateAttribute()
    {
        return $this->created_at?->format('d F Y');
    }

    public static function getPermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->select('group_name as name')
            ->groupBy('group_name')
            ->get();
        return $permission_groups;
    }

    public static function getPermissionGroup()
    {
        return $permission_groups = DB::table('permissions')
            ->select('group_name')
            ->groupBy('group_name')
            ->get();
    }
    public static function getPermissionsByGroupName($group_name)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_name', $group_name)
            ->get();
        return $permissions;
    }

    public static function roleHasPermissions($role, $permissions)
    {
        $hasPermission = true;
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $hasPermission = false;
                return $hasPermission;
            }
        }
        return $hasPermission;
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Plan', 'plan_id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    public function card_orders()
    {
        return $this->hasMany('App\Models\CardOrder');
    }

    // make password mutator for encrypt
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }


    //scopes
    public function scopeAdmin($query): Builder // Admins list
    {
        return $query->where('role', 'admin');
    }

    public function scopeUser($query): Builder //Candidates list
    {
        return $query->where('role', 'user');
    }

    public function scopeActive($query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeInActive($query): Builder
    {
        return $query->where('status', '!=', 1);
    }

    public function scopeVerified($query): Builder
    {
        return $query->where('email_verified_at', '!=', null);
    }

    public function isAdmin(): bool
    {
        return $this->role == 'admin';
    }

    public function getDashboardRoute(): string // route name
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'user' => 'user.dashboard',
            default => 'login',
        };
    }

    // helper
    public function validatePlan(string $planKey, bool $toArray = false)
    {
        return PlanPerks::checkPlan($planKey, $toArray);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    public function credit_cards(): HasMany
    {
        return $this->hasMany(CreditCard::class);
    }
    public function cardholder(): HasOne
    {
        return $this->hasOne(CardHolder::class);
    }

    public function kycMethods(): BelongsToMany
    {
        return $this->belongsToMany(KycMethod::class)->withPivot('kyc_request_id');
    }

    public function setLocation(): bool
    {
        // return $this->country_id && $this->city_id && $this->state_id && $this->address_line;
        return $this->country_id && $this->address_line;
    }

    public function hasVerifiedPhone(): bool
    {
        return (bool) ($this->phone && $this->phone_verified_at);
    }
    public function hasVerifiedEmail(): bool
    {
        return (bool) ($this->email && $this->email_verified_at);
    }
    public function hasVerifiedKYC(): bool
    {
        return (bool) $this->kyc_verified_at;
    }

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return $this->google2fa_secret !== null;
    }

    public function enableTwoFactorAuthentication(): bool
    {
        $google2fa = new Google2FA();
        $this->google2fa_secret = $google2fa->generateSecretKey();
        return $this->save();
    }
    public function disableTwoFactorAuthentication(): bool
    {
        $this->google2fa_secret = null;
        $this->google2fa_ts = null;
        return $this->save();
    }

    public function getCurrencyWallets()
    {
        $currencies = VirtualCurrency::query()
            ->with('rates.exchange_currency:id,currency')
            ->where('status', 'active')
            ->get();
        $wallets = Wallet::query()
            ->where('user_id', $this->id)
            ->get();

        $currency_wallets = [];

        foreach ($currencies as $currency) {
            $wallet = $wallets->where('virtual_currency_id', $currency->id)->first();
            if ($wallet) {
                $currency_wallets[] = [
                    'currency_id' => $currency->id,
                    'wallet_id' => $wallet->id,
                    'currency_name' => $currency->currency,
                    'rates' => $currency->rates,
                    'preview' => $currency->preview,
                    'balance' => $wallet->balance,
                    'is_default' => $currency->is_default,
                    'precision' => $currency->precision
                ];
            }
        }

        return $currency_wallets ?? [];
    }


    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function assignedemails(): HasOne
    {
        return $this->hasOne(CardEmail::class);
    }
}
