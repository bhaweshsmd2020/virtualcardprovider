<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\VirtualCurrency;
use App\Models\Wallet;
use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\Color\Rgb;

class AccountController extends Controller
{
    public function index()
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-dollar"></i>&nbsp&nbsp' . __('P2P Transfer'),
                'url' => route('user.withdraw.create'),
            ],
            [
                'name' => '<i class="bx bx-credit-card"></i>&nbsp&nbsp' . __('Topup wallet'),
                'url' => route('user.top-up.create-wallet'),
            ],
            [
                'name' => '<i class="bx bx-transfer"></i>&nbsp&nbsp' . __('Transfer'),
                'url' => route('user.transfer.index'),
            ]
        ];
        $currencies = VirtualCurrency::query()
            ->with('rates.exchange_currency')
            ->where('status', 'active')
            ->get();
        $wallets = Wallet::query()
            ->where('user_id', auth()->id())
            ->get();

        foreach ($currencies as $currency) {
            $wallet = $wallets->where('virtual_currency_id', $currency->id)->first();
            $has_wallet = $wallet ? true : false;
            $currency_wallets[] = [
                'currency' => $currency->toArray(),
                'wallet' => $has_wallet ? $wallet->toArray() : null,
            ];
        }
        $qrCodeLink = url('/user/top-ups/' . auth()->user()->uuid . '/payment');
        $qrCode = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(
                    new Rgb(255, 255, 255),
                    new Rgb(2, 6, 23)
                )),
                new SvgImageBackEnd
            )
        ))->writeString($qrCodeLink);
        $card_intro_details = get_option('card_intro_details');
        return Inertia::render('User/Wallet', [
            'buttons' => $buttons,
            'accounts' => $currency_wallets ?? [],
            'qrCode' => $qrCode,
            'qrCodeLink' => $qrCodeLink,
            'card_intro_details' => $card_intro_details
        ]);
    }

    public function account()
    {
        $buttons = [
            [
                'name' => '<i class="bx bx-plus"></i>&nbsp&nbsp' . __('Topup Wallet Balance'),
                'url' => route('user.top-up.create'),
            ],
            // [
            //     'name' => '<i class="bx bx-transfer"></i>&nbsp&nbsp' . __('Transfer'),
            //     'url' => route('user.transfer.index'),
            // ]
        ];
        $currencies = VirtualCurrency::query()
            ->with('rates.exchange_currency')
            ->where('status', 'active')
            ->get();
        $currency_wallets = User::query()
            ->where('id', auth()->id())
            ->get();
        $qrCodeLink = url('/user/top-ups/' . auth()->user()->uuid . '/payment');
        $qrCode = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(
                    new Rgb(255, 255, 255),
                    new Rgb(2, 6, 23)
                )),
                new SvgImageBackEnd
            )
        ))->writeString($qrCodeLink);
        $card_intro_details = get_option('card_intro_details');
        return Inertia::render('User/Account', [
            'buttons' => $buttons,
            'accounts' => $currency_wallets ?? [],
            'qrCode' => $qrCode,
            'qrCodeLink' => $qrCodeLink,
            'card_intro_details' => $card_intro_details
        ]);
    }
}
