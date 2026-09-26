<?php

namespace App\Filament\Franchise\Resources;

use App\Filament\Franchise\Resources\StudyMaterialResource\Pages;
use App\Models\Batch;
use App\Models\Course;
use App\Models\StudyMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudyMaterialResource extends Resource
{
    protected static ?string $model = StudyMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Resource & Learning Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Chapter 4: Object-Oriented PHP Notes & Code Samples'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'pdf' => 'PDF Document / E-Book',
                                'document' => 'Word / PPT / Spreadsheet',
                                'video_link' => 'Video Lecture (YouTube / Vimeo / Loom)',
                                'notes' => 'Markdown / Text Lecture Notes',
                            ])
                            ->default('pdf')
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('course_id')
                            ->relationship('course', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\Select::make('batch_id')
                            ->label('Target Batch (Leave empty for all batches of this course)')
                            ->options(fn(Forms\Get $get) => Batch::where('course_id', $get('course_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Upload File / Document')
                            ->directory('study-materials')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'image/*'])
                            ->visible(fn(Forms\Get $get) => in_array($get('type'), ['pdf', 'document']))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('external_url')
                            ->label('Video or External Resource URL')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->visible(fn(Forms\Get $get) => $get('type') === 'video_link')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->placeholder('Content overview, key topics, or instructions for students...')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published to Students')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'video_link' => 'warning',
                        'document' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch.name')
                    ->label('Batch')
                    ->default('All Course Batches')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'pdf' => 'PDF',
                        'video_link' => 'Video Link',
                        'document' => 'Document',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudyMaterials::route('/'),
            'create' => Pages\CreateStudyMaterial::route('/create'),
            'edit' => Pages\EditStudyMaterial::route('/{record}/edit'),
        ];
    }
}
