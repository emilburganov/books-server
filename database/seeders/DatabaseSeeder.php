<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory(10)->create();

        $genres = ['Фантастика', 'Комедия', 'Советы от гуру', 'Фэнтези', 'История'];
        foreach ($genres as $genre) {
            Genre::query()->create([
                'title' => $genre
            ]);
        }

        $authors = ['Бурашев Ш.М', 'Бурганов Э.Э', 'Джафарли О.А', 'Закиров Д.Х', 'Асадуллин Т.Э', 'Петров Е.А', 'Кирильчук А.Н'];
        foreach ($authors as $author) {
            Author::query()->create([
                'name' => $author
            ]);
        }

        $books = Book::factory(10)->create();
        foreach ($books as $book) {
            $book->authors()->attach([1, 2, 3]);
            $book->genres()->attach([1, 2, 3]);
        }
    }
}
