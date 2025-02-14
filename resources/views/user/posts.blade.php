@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Semua Postingan Anda</h2>

    @if($posts->isEmpty())
        <p>Anda belum membuat postingan.</p>
    @else
        <div class="row">
            @foreach($posts as $post)
                <div class="col-md-6">
                    <div class="card mb-3 shadow border-0">
                        <div class="card-body">
                            <p><strong>Isi Postingan:</strong></p>
                            <p>{{ $post->content }}</p>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-start mt-3">
                                <!-- Button to view details of the post -->
                                <a href="{{ route('post.show', $post->id) }}" class="btn btn-primary btn-sm me-2">Lihat Detail</a>

                                <!-- Button to edit the post -->
                                <a href="{{ route('post.edit', $post->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>

                                <!-- Form to delete the post -->
                                <form action="{{ route('post.destroy', $post->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

