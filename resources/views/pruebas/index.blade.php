<h1>{{$titulo}}</h1>
<hr>

<ul>
    @foreach ($animales as $animal)
        <li>{{$animal}}</li>
    @endforeach
</ul>
