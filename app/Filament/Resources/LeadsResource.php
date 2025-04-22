<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadsResource\Pages;
use App\Models\Insurance;
use App\Models\Lead;
use App\Models\Status;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeadsResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_id')
                    ->label('Status')
                    ->options(Status::pluck('name', 'id'))
                    ->visible(fn(): bool => Auth::user()?->hasRole('admin') ?? false),
                Forms\Components\Select::make('insurance_id')
                    ->relationship('insurance', 'name')
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('patient_phone')
                    ->tel()
                    ->unique()
                    ->validationMessages([
                        'unique' => 'The number is already present'
                    ])
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('secondary_phone')
                    ->tel()
                    ->maxLength(15)
                    ->default(null),
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(15),
                Forms\Components\DatePicker::make('dob')
                    ->required(),
                Forms\Components\TextInput::make('medicare_id')
                    ->unique()
                    ->validationMessages([
                        'unique' => 'The Medicare Id is already present'
                    ])
                    ->required()
                    ->maxLength(15),
                Forms\Components\Textarea::make('address')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('state')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('zip')
                    ->required()
                    ->maxLength(15),
                Forms\Components\Textarea::make('product_specs')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('doctor_name')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('patient_last_visit')
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('doctor_address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('doctor_phone')
                    ->tel()
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('doctor_fax')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('doctor_npi')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Textarea::make('recording_link')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('comments')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id())
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($user) {
                if ($user->hasRole('admin')) {
                    return $query;
                }

                if ($user->hasRole('center')) {
                    return $query->where('user_id', $user->id);
                }

                if ($user->hasRole('agent')) {
                    return $query->where('assigned_to_id', $user->id);
                }

                throw new \Exception('Unauthorized access');
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->date()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Center')
                    ->formatStateUsing(function ($state) {
                        return $state ? ucwords($state) : 'N/A';
                    })
                    ->extraAttributes(['class' => 'width-full'])
                    ->sortable()
                    ->searchable(
                        query: fn(Builder $query, string $search) => $query->whereHas(
                            'user',
                            fn($q) => $q->where('name', 'like', "%{$search}%")
                        )
                    )->hidden(fn(): bool => !$user->hasRole('admin')),
                Tables\Columns\SelectColumn::make('assigned_to_id')
                    ->label('Assigned To')
                    ->options(User::where('role', 'agent')->pluck('name', 'id'))
                    ->extraAttributes(['class' => 'width-full'])
                    ->searchable()
                    ->disabled(fn() => !$isAdmin)
                    ->hidden(fn(): bool => !$user->hasRole('admin')),
                $isAdmin
                    ? Tables\Columns\SelectColumn::make('status_id')
                    ->label('Status')
                    ->options(Status::pluck('name', 'id')) // or your options source
                    ->extraAttributes(['class' => 'width-full'])
                    ->searchable()
                    ->disabled(fn() => !$isAdmin)
                    : Tables\Columns\TextColumn::make('status.name')
                    ->placeholder('Unassigned')
                    ->badge('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('insurance.name')
                    ->numeric()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->numeric()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient_phone')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('secondary_phone')
                    ->placeholder('-')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->copyable()
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('medicare_id')
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
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_address')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('patient_last_visit')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_phone')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_fax')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor_npi')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->visible(fn(): bool => auth()->user()->isCenter())
                    ->searchable(),
                Tables\Columns\TextInputColumn::make('notes')
                    ->visible(fn(): bool => !auth()->user()->hasRole('center'))
                    ->rules(['required', 'max:255'])
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
                Tables\Filters\SelectFilter::make('status_id')
                    ->label('Status')
                    ->options(Status::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Center')
                    ->options(User::whereIn('role', ['center'])->pluck('name', 'id'))
                    ->visible(fn(): bool => auth()->user()->hasRole('admin')),
                Tables\Filters\SelectFilter::make('insurance_id')
                    ->label('Insurance')
                    ->options(Insurance::pluck('name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn(): bool => auth()->user()->hasRole('admin') || auth()->user()->hasRole('center')),
                ]),
            ]);
    }

    public static function canEdit(Model $record): bool
    {
        return !auth()->user()->hasRole('agent') && !auth()->user()->hasRole('manager');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('center');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLeads::route('/create'),
            'edit' => Pages\EditLeads::route('/{record}/edit'),
        ];
    }
}
