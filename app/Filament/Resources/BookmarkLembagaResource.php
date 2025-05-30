<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookmarkLembagaResource\Pages;
use App\Filament\Resources\BookmarkLembagaResource\RelationManagers;
use App\Models\BookmarkLembaga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookmarkLembagaResource extends Resource
{
    protected static ?string $model = BookmarkLembaga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->disabled(),
                Forms\Components\Select::make('lembaga_id')
                    ->relationship('lembaga', 'nama')
                    ->required()
                    ->disabled(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
            ])
            ->actions([]) // Hapus semua aksi (edit, delete)
            ->bulkActions([]); // Hapus bulk actions
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
            'index' => Pages\ListBookmarkLembagas::route('/'),
        ];
    }
}
