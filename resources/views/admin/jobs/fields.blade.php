<!-- Jb Sc Id Index Field -->
<div class="form-group col-sm-12">
    {!! Form::label('jb_sc_id_index', 'Jb Sc Id Index:') !!}
    {!! Form::text('jb_sc_id_index', null, ['class' => 'form-control']) !!}
</div>

<!-- Conpany Field -->
<div class="form-group col-sm-12">
    {!! Form::label('conpany', 'Conpany:') !!}
    {!! Form::text('conpany', null, ['class' => 'form-control']) !!}
</div>

<!-- Nr Field -->
<div class="form-group col-sm-12">
    {!! Form::label('nr', 'Nr:') !!}
    {!! Form::text('nr', null, ['class' => 'form-control']) !!}
</div>

<!-- Padmin Field -->
<div class="form-group col-sm-12">
    {!! Form::label('padmin', 'Padmin:') !!}
    {!! Form::text('padmin', null, ['class' => 'form-control']) !!}
</div>

<!-- Dateopenm Field -->
<div class="form-group col-sm-12">
    {!! Form::label('dateopenm', 'Dateopenm:') !!}
    {!! Form::date('dateopenm', null, ['class' => 'form-control']) !!}
</div>

<!-- Dateopen Field -->
<div class="form-group col-sm-12">
    {!! Form::label('dateopen', 'Dateopen:') !!}
    {!! Form::text('dateopen', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Jpaytype Field -->
<div class="form-group col-sm-12">
    {!! Form::label('jpaytype', 'Jpaytype:') !!}
    {!! Form::number('jpaytype', null, ['class' => 'form-control']) !!}
</div>

<!-- Jstatus Field -->
<div class="form-group col-sm-12">
    {!! Form::label('jstatus', 'Jstatus:') !!}
    {!! Form::number('jstatus', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12 text-center">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('admin.jobs.index') !!}" class="btn btn-secondary">Cancel</a>
</div>
