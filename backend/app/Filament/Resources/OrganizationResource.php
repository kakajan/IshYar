<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Models\Organization;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Organization';
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Components\Section::make('Basic Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Components\FileUpload::make('logo')
                            ->image()
                            ->directory('organizations/logos'),
                    ])
                    ->columns(2),

                Components\Section::make('Localization')
                    ->schema([
                        Components\Select::make('timezone')
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz]))
                            ->searchable()
                            ->default('UTC'),
                        Components\Select::make('default_locale')
                            ->options([
                                'en' => 'English',
                                'fa' => 'فارسی (Persian)',
                            ])
                            ->default('en'),
                        Components\Select::make('default_currency')
                            ->options([
                                'USD' => 'US Dollar (USD)',
                                'EUR' => 'Euro (EUR)',
                                'GBP' => 'British Pound (GBP)',
                                'IRR' => 'Iranian Rial (IRR)',
                                'IRT' => 'Iranian Toman (IRT)',
                            ])
                            ->default('USD'),
                    ])
                    ->columns(3),

                Components\Section::make('Status')
                    ->schema([
                        Components\Toggle::make('is_active')
                            ->default(true),
                    ]),

                Components\Section::make('Settings')
                    ->schema([
                        Components\KeyValue::make('settings')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('default_locale')
                    ->badge(),
                Tables\Columns\TextColumn::make('default_currency')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view'   => Pages\ViewOrganization::route('/{record}'),
            'edit'   => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
