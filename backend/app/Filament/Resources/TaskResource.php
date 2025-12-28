<?php
namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms\Components;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Task Management';
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Components\Section::make('Task Information')
                    ->schema([
                        Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Components\RichEditor::make('description')
                            ->columnSpanFull(),
                        Components\Select::make('type')
                            ->options([
                                'routine'     => 'Routine',
                                'situational' => 'Situational',
                            ])
                            ->default('situational')
                            ->required(),
                        Components\Select::make('status')
                            ->options([
                                'pending'     => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed'   => 'Completed',
                                'on_hold'     => 'On Hold',
                                'cancelled'   => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Components\Select::make('priority')
                            ->options([
                                'low'    => 'Low',
                                'medium' => 'Medium',
                                'high'   => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required(),
                    ])
                    ->columns(3),

                Components\Section::make('Assignment')
                    ->schema([
                        Components\Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Components\Select::make('creator_id')
                            ->relationship('creator', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(auth()->id()),
                        Components\Select::make('assignee_id')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Components\Select::make('approver_id')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),

                Components\Section::make('Timing')
                    ->schema([
                        Components\DateTimePicker::make('due_date'),
                        Components\DateTimePicker::make('start_date'),
                        Components\DateTimePicker::make('completed_at'),
                        Components\TextInput::make('estimated_hours')
                            ->numeric()
                            ->step(0.5),
                        Components\TextInput::make('progress')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])
                    ->columns(3),

                Components\Section::make('Recurrence')
                    ->schema([
                        Components\Toggle::make('is_recurring')
                            ->live(),
                        Components\KeyValue::make('recurrence_rule')
                            ->visible(fn(Get $get): bool => $get('is_recurring'))
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Components\Section::make('Approval')
                    ->schema([
                        Components\Toggle::make('requires_approval'),
                        Components\Select::make('approval_status')
                            ->options([
                                'pending'  => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Components\Section::make('Tags')
                    ->schema([
                        Components\TagsInput::make('tags'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'routine'     => 'info',
                        'situational' => 'warning',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'on_hold'     => 'warning',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low'    => 'gray',
                        'medium' => 'info',
                        'high'   => 'warning',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('progress')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn(Task $record): string =>
                        $record->due_date && $record->due_date->isPast() && $record->status !== 'completed'
                            ? 'danger'
                            : 'gray'
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'routine'     => 'Routine',
                        'situational' => 'Situational',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'     => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'on_hold'     => 'On Hold',
                        'cancelled'   => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\SelectFilter::make('assignee')
                    ->relationship('assignee', 'name'),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index'  => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view'   => Pages\ViewTask::route('/{record}'),
            'edit'   => Pages\EditTask::route('/{record}/edit'),
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
