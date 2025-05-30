<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected function getFormSchema(): array
    {
        return [
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ];
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['role'] = 'admin'; // Set role otomatis menjadi admin
        $data['password'] = Hash::make($data['password']);
        return $data;
    }
}