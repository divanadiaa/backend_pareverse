<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LembagaResource\Pages;
use App\Filament\Resources\LembagaResource\RelationManagers;
use App\Models\Lembaga;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LembagaResource extends Resource
{
    protected static ?string $model = Lembaga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required(),
                Forms\Components\RichEditor::make('deskripsi')
                    ->required(),
                Forms\Components\FileUpload::make('gambar')
                    ->image()
                    ->directory('lembagas')
                    ->disk('public')
                    ->visibility('public'),
                Forms\Components\TextInput::make('alamat')
                    ->required(),
                Forms\Components\TextInput::make('link_maps')
                    ->url(),
                Forms\Components\TextInput::make('whatsapp')
                    ->required()
                    ->regex('/^(\+62|62|0)[0-9]{9,12}$/') // Validasi nomor Indonesia
                    ->label('Nomor WhatsApp'),
                Forms\Components\Toggle::make('is_recommended')
                    ->label('Rekomendasi Admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->html()
                    ->tooltip(function ($record) {
                        return strip_tags($record->deskripsi); // Tooltip untuk menampilkan deskripsi lengkap
                    }),
                Tables\Columns\ImageColumn::make('gambar')
                    ->disk('public') // Sesuaikan dengan disk storage yang digunakan
                    ->size(50), // Ukuran thumbnail gambar
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link_maps')
                    ->url(fn ($record) => $record->link_maps) // Membuat link_maps sebagai tautan yang dapat diklik
                    ->openUrlInNewTab()
                    ->limit(30),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->searchable()
                    ->url(function ($record) {
                        // Pastikan nomor dalam format internasional tanpa tanda plus, spasi, atau karakter lain
                        $phoneNumber = preg_replace('/[^0-9]/', '', $record->whatsapp); // Hapus karakter non-angka
                        if (substr($phoneNumber, 0, 1) === '0') {
                            $phoneNumber = '62' . substr($phoneNumber, 1); // Ganti 0 dengan kode negara +62
                        }
                        return 'https://wa.me/' . $phoneNumber; // Kembalikan URL WhatsApp
                    })
                    ->openUrlInNewTab(),
                Tables\Columns\BooleanColumn::make('is_recommended')
                    ->label('Rekomendasi Admin'),
                Tables\Columns\TextColumn::make('avg_rating')
                    ->label('Rata-rata Rating')
                    ->getStateUsing(function ($record) {
                        return $record->reviews()->avg('rating') ?? 'Belum ada rating';
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_recommended') // Tombol Rekomendasi
                    ->label('Rekomendasi Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->withAvg('reviews', 'rating');
            });
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
            'index' => Pages\ListLembagas::route('/'),
            'create' => Pages\CreateLembaga::route('/create'),
            'edit' => Pages\EditLembaga::route('/{record}/edit'),
        ];
    }
}