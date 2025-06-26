<h3>Your KYC Has Been @if($status == 'approve') Approved @else Rejected @endif!</h3>
<p>Dear {{ $name }},</p>
<p>Congratulations! Your KYC (Know Your Customer) verification has been successfully @if($status == 'approve') approved. You can now access all the features and benefits of your account. @else rejected. @endif</p>
<p>To log in to your account, click the button below:</p>
<a href="{{ route('login') }}" class="button">Login to Your Account</a>
<p>If you did not request this, please ignore this email.</p>

<p>{{ __('Thanks') }},</p>
<p>{{ env('APP_NAME') }}.</p>