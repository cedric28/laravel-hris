@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors as $error)
                <li>Row {{ $error['row'] }}: {{ implode(', ', $error['errors']) }}</li>
            @endforeach
        </ul>
    </div>
@endif