@php
    $m = $mieter ?? null;
@endphp
<div class="space-y-6">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $m?->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>
    <div>
        <x-input-label for="email" :value="__('Email (optional)')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $m?->email)" />
        <p class="mt-1 text-xs text-gray-500">{{ __('Must be unique if you enable the tenant portal (login is by email).') }}</p>
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>
    <div class="border-t border-gray-200 pt-6 mt-6 space-y-4">
        <div>
            <h3 class="text-sm font-medium text-gray-900">{{ __('Tenant portal') }}</h3>
            <p class="text-sm text-gray-600 mt-1">
                {{ __('Tenants can sign in at :url', ['url' => route('portal.login')]) }}
            </p>
        </div>
        <div>
            <x-input-label for="portal_password" :value="__('Portal password')" />
            <x-text-input id="portal_password" name="portal_password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('portal_password')" />
        </div>
        <div>
            <x-input-label for="portal_password_confirmation" :value="__('Confirm portal password')" />
            <x-text-input id="portal_password_confirmation" name="portal_password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        </div>
        @if ($m?->password)
            <div class="flex items-center gap-2">
                <input id="disable_portal" name="disable_portal" type="checkbox" value="1" class="rounded border-gray-300" @checked(old('disable_portal')) />
                <x-input-label for="disable_portal" :value="__('Disable portal access (remove password)')" class="!mb-0" />
            </div>
        @endif
    </div>
</div>
