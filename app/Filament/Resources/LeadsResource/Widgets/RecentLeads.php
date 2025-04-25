<?php

namespace App\Filament\Resources\LeadsResource\Widgets;

use App\Models\Lead;
use App\Models\Status;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeads extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isManager = $user->hasRole('manager');
        $isCenter = $user->hasRole('center');

        $query = match ($user->role) {
            'admin' => Lead::orderBy('created_at', 'desc')
                ->limit(10),
            'manager' => Lead::whereHas('user', fn($q) => $q->where('team_lead_id', $user->id)),
            'center' => Lead::where('user_id', $user->id),
            'agent' => Lead::where('assigned_to_id', $user->id),
            default => Lead::query(),
        };

        return $table
            ->query(
                $query
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->date()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Center')
                    ->formatStateUsing(fn($state) => $state ? ucwords($state) : 'N/A')
                    ->extraAttributes(['class' => 'width-full'])
                    ->sortable()
                    ->searchable(
                        query: fn($query, string $search) => $query->whereHas(
                            'user',
                            fn($q) => $q->where('name', 'like', "%{$search}%")
                        )
                    )
                    ->hidden(!$isAdmin),

                Tables\Columns\SelectColumn::make('assigned_to_id')
                    ->label('Assigned To')
                    ->options(
                        $isAdmin
                            ? User::where('role', 'agent')->pluck('name', 'id')
                            : User::where('role', 'agent')
                            ->where('team_lead_id', $user->id)
                            ->pluck('name', 'id')
                    )
                    ->extraAttributes(['class' => 'width-full'])
                    ->searchable()
                    ->hidden(!$isAdmin && !$isManager),

                $isAdmin
                    ? Tables\Columns\SelectColumn::make('status_id')
                    ->label('Status')
                    ->options(Status::pluck('name', 'id'))
                    ->extraAttributes(['class' => 'width-full'])
                    ->searchable()
                    ->disabled(!$isAdmin)
                    : Tables\Columns\TextColumn::make('status.name')
                    ->label('Status')
                    ->placeholder('Unassigned')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('insurance.name')
                    ->label('Insurance')
                    ->numeric()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->numeric()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('patient_phone')
                    ->label('Phone')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('secondary_phone')
                    ->label('Secondary Phone')
                    ->placeholder('-')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('First Name')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->label('Last Name')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('dob')
                    ->label('Date of Birth')
                    ->copyable()
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('medicare_id')
                    ->label('Medicare ID')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('city')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('state')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('zip')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_name')
                    ->label('Doctor Name')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_address')
                    ->label('Doctor Address')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('patient_last_visit')
                    ->label('Last Visit')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_phone')
                    ->label('Doctor Phone')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_fax')
                    ->label('Doctor Fax')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor_npi')
                    ->label('Doctor NPI')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('notes')
                    ->visible($isCenter)
                    ->searchable(),

                Tables\Columns\TextInputColumn::make('notes')
                    ->visible(!$isCenter)
                    ->rules(['max:255'])
            ]);
    }
}
