<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewLembagaResource\Pages;
use App\Filament\Resources\ReviewLembagaResource\RelationManagers;
use App\Models\ReviewLembaga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewLembagaResource extends Resource
{
    protected static ?string $model = ReviewLembaga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->disabled(), // Admin tidak boleh mengubah user_id
                Forms\Components\Select::make('lembaga_id')
                    ->relationship('lembaga', 'nama')
                    ->required()
                    ->disabled(), // Admin tidak boleh mengubah lembaga_id
                Forms\Components\Select::make('rating')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('komentar')
                    ->maxLength(1000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lembaga.nama')
                    ->label('Lembaga')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->sortable(),
                Tables\Columns\TextColumn::make('komentar')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->komentar),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReviewLembagas::route('/'),
            'create' => Pages\CreateReviewLembaga::route('/create'),
            'edit' => Pages\EditReviewLembaga::route('/{record}/edit'),
        ];
    }
}
