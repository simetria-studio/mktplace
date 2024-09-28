@if ($tipo_home == 'home')
    <section class="conatainer-fluid mt-2 top-footer d-none d-md-block" style="background-color: #FD9108">
        <div class="container">
            <div class="row">
                <div class="col-3 d-flex align-items-center mb-1"><a href="{{route('products')}}" class="btn btn-produto">VER TODOS OS PRODUTOS</a></div>
                {{-- Campo de pesquisa --}}
                <div class="col-9 mb-1">
                    <form action="{{route('search')}}" method="get">
                        <div class="input-group">
                            <input type="search" name="q" value="@isset($_GET['q']){{$_GET['q']}}@endisset" class="form-control" placeholder="O que você procura hoje?">
                            <select name="c" class="form-control" style="font-size: 0.9em;">
                                <option value="">Todas as Categorias</option>
                                @foreach (getCategories('0') as $category)
                                    <option value="{{$category->slug}}" @isset($_GET['c']) @if($_GET['c'] == $category->slug) selected @endif @endisset>{{mb_convert_case($category->name,MB_CASE_TITLE)}}</option>
                                    </li>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-dark"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                {{-- Categorias --}}
                <div class="col-12">
                    <ul class="menu-categorias">
                        @foreach (getCategories('0') as $category)
                            @php
                                $image      = Storage::get($category->icon);
                                $mime_type  = Storage::mimeType($category->icon);
                                $image      = 'data:'.$mime_type.';base64,'.base64_encode($image);
                            @endphp
                            {{-- <li class="categoria d-none" title="{{mb_convert_case($category->name,MB_CASE_TITLE)}}">
                                <a href="{{route('category', $category->slug)}}" class="">
                                    <span class="img"><img src="{{$image}}" alt=""></span>
                                    <span class="title">{{mb_convert_case($category->name,MB_CASE_TITLE)}}</span>
                                </a>
                            </li> --}}
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@else
    <style>
        .menu-categorias .slick-prev:before {
            color: #000;
        }
        .menu-categorias .slick-next:before {
            color: #000;
        }
    </style>
    <section class="conatainer-fluid mt-2 top-footer d-none d-md-block">
        <div class="container">
            <div class="row">
                {{-- Categorias --}}
                <div class="col-12">
                    <ul class="menu-categorias">
                        @foreach (getCategories('1') as $category)
                            @php
                                $image      = Storage::get($category->icon);
                                $mime_type  = Storage::mimeType($category->icon);
                                $image      = 'data:'.$mime_type.';base64,'.base64_encode($image);
                            @endphp
                            <li class="categoria" title="{{mb_convert_case($category->name,MB_CASE_TITLE)}}">
                                <a href="{{route('category.service', $category->slug)}}" class="text-dark">
                                    <span class="img"><img src="{{$image}}" alt=""></span>
                                    <span class="title">{{mb_convert_case($category->name,MB_CASE_TITLE)}}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endif