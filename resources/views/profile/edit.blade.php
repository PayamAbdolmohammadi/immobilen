<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold leading-tight text-gray-950">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-app-card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-app-card>

        <x-app-card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-app-card>

        <x-app-card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-app-card>
    </div>
</x-app-layout>
