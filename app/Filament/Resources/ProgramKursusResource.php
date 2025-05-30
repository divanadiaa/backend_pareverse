<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramKursusResource\Pages;
use App\Filament\Resources\ProgramKursusResource\RelationManagers;
use App\Models\ProgramKursus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramKursusResource extends Resource
{
    protected static ?string $model = ProgramKursus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lembaga_id')
                    ->relationship('lembaga', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('nama_program')
                    ->required(),
                Forms\Components\TextInput::make('bahasa')
                    ->required(),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->formatStateUsing(function ($state) {
                        return $state ? 'Rp ' . number_format($state, 0, ',', '.') : null;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return (float) str_replace(['Rp ', '.'], '', $state);
                    })
                    ->helperText('Masukkan harga dalam format angka, misalnya 750000 untuk Rp 750.000'),
                Forms\Components\TextInput::make('durasi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lembaga.nama')
                    ->label('Lembaga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bahasa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->formatStateUsing(function ($state) {
                        return 'Rp ' . number_format($state, 0, ',', '.');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('durasi')
                    ->searchable(),
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
            'index' => Pages\ListProgramKursuses::route('/'),
            'create' => Pages\CreateProgramKursus::route('/create'),
            'edit' => Pages\EditProgramKursus::route('/{record}/edit'),
        ];
    }
}
