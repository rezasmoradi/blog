@extends('partials.header')

@include('partials.navbar')

<div class="container">
    <div class="d-flex flex-row justify-content-around align-items-center my-4">
        {{--@if($posts)
            @foreach($posts as $post)
                <a href="/post/watch/{{$post->id}}" class="text-decoration-none text-dark">
                    <div class="card mx-3" style="width: 18rem; min-height: 10rem;">
                        <div class="card-body">
                            <h5 class="card-title ellipsis">{{$post->title}}</h5>
                            Hello
                            <p class="card-text">
                                {!! strlen($post->body) >= 50 ? substr($post->body, 0, 50) . '...' : $post->body !!}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif--}}
    </div>
</div>

@extends('partials.footer')