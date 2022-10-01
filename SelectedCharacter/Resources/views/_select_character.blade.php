<h1>
    Selected Character
</h2>

<p>You can select one of your characters to be featured on your profile here.</p>
{!! Form::open(['url' => 'characters/select-character']) !!}
    {!! Form::select('character_id', $characters->pluck('fullName', 'id'), Auth::user()->settings->selected_character_id, ['class' => 'form-control mb-2 default character-select', 'placeholder' => 'Select Character']) !!}
    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}
