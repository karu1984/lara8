<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Book;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;


class BookIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $liveModal = false;//モーダルウインドウ
    public $title;//タイトル
    public $newImage;//画像
    public $price;//価格
    public $description;//詳細

    public $Id;
    public $oldImage;
    public $editWork = false;

    public $search="";

    public function bookPost(){
        $this->validate([
            'title' => 'required',
            'newImage' => 'image|max:2048',
            'price' => 'integer|required',
            'description' => 'required',
        ]);
        $image = $this->newImage->store('public/books');
        Book::create([
            'title' => $this->title,
            'image' => $image,
            'price' => $this->price,
            'description' => $this->description,
        ]);
        $this->reset();
    }

    public function render(){
        if( $this->search!="" ){
            return view('livewire.book-index', [
                'books' => Book::Where('title','like','%'.$this->search.'%')
                ->orderBy('id','DESC')->paginate(3),
            ]);
        }else{
            return view('livewire.book-index', [
                'books' => Book::select('id','title','price','image','description')
                ->orderBy('id','DESC')->paginate(3),
            ]);
        }
    }

    public function showBookModal(){
        $this->reset();
        $this->liveModal = true;
    }

    public function showEditBookModal($id){
        $book = Book::findOrFail($id);
        $this->Id = $book->id;
        $this->title = $book->title;
        $this->oldImage = $book->image;
        $this->price = $book->price;
        $this->description = $book->description;
        $this->editWork = true;
        $this->liveModal = true;
    }
    public function updateBook($Id){
        $this->validate([
            'title' => 'required',
            'price' => 'integer|required',
            'description' => 'required',
        ]);
        if($this->newImage){
            $image = $this->newImage->store('public/books');             
            Book::where('id',$Id)->update([          
                    'title' => $this->title,
                    'image' => $image,
                    'price' => $this->price,
                    'description' => $this->description,
            ]);
        }else{         
            Book::where('id',$Id)->update([          
                    'title' => $this->title,
                    'price' => $this->price,
                    'description' => $this->description,
            ]);
        }
        session()->flash('message','更新しました！');
    }
    public function deleteBook($id){
        $book = Book::findOrFail($id);
        Storage::delete($book->image);
        $book->delete();
        $this->reset();
    }


}
