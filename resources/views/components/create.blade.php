@extends('layout')
@section('title', 'Components')
@section('subtitle', 'Components')
<style>
    .block {
        display: block;
    }

    input {
        width: 50%;
        display: inline-block;
    }

    span {
        display: inline-block;
        cursor: pointer;
        text-decoration: underline;
    }

    .d-none {
        display: none;
    }
</style>
@section('content')
<div class="col-lg-10 mx-auto">
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{route('components.store')}}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-5 mt-4">
                    <label for="component_name" class="col-sm-3 col-form-label required">Component Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="component_name" id="component_name">
                        @if ($errors->has('component_name'))
                        <span style="font-size: 12px;" class="text-danger">{{ $errors->first('component_name') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-5 mt-4">
                    <label for="path" class="col-sm-3 col-form-label required">Path</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="path" id="path">
                        @if ($errors->has('path'))
                        <span style="font-size: 12px;" class="text-danger">{{ $errors->first('path') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-5 mt-4">
                    <label for="type" class="col-sm-3 col-form-label required">Type</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="type" id="type">
                        @if ($errors->has('type'))
                        <span style="font-size: 12px;" class="text-danger">{{ $errors->first('type') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-5">
                    <label for="category" class="col-sm-3 col-form-label required ">Category</label>
                    <div class="col-sm-9">
                        <select name="category[]" class="form-select" id="category" multiple size="3">
                            <option>Select Category</option>
                            @foreach ($category as $data)
                            <option value="{{$data['name']}}">
                                {{$data['name']}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('category'))
                    <span style="font-size: 12px;" class="text-danger">{{ $errors->first('category') }}</span>
                    @endif
                </div>
                <div class="row mb-5">
                    <label for="preview" class="col-sm-3 col-form-label">Preview Image</label>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" name="preview" id="preview" multiple>
                    </div>
                    @if ($errors->has('preview.*'))
                    @foreach($errors->get('preview.*') as $key => $errorMessages)
                    @foreach($errorMessages as $error)
                    <span style="font-size: 12px; padding: 10px 100px;" class="text-danger">
                        @if ($error == 'The document failed to upload.')
                        {{$error}} The document may not be greater than 5 mb.
                        @else
                        {{$error}}
                        @endif
                    </span>
                    @endforeach
                    @endforeach
                    @endif
                </div>
                <div class="row mb-5 mt-4">
                    <div class="col-md-3">
                        <label>Dependency:</label>
                    </div>
                </div>
                <div class="dependencies-container">
                </div>
                <span class="js-add-dependency clone text-success" style="font-size: 20px;">+</span>

                <div class="row mb-5 mt-4">
                    <div class="col-md-3">
                        <label>Form Fields:</label>
                    </div>
                </div>

                <div class="form-fields-container">
                </div>
                <span class="js-add-form-fields clone text-success" style="font-size: 20px;">+</span>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="js-hidden-dependency-option d-none">
    <div class="row mb-2 js-dependency-option">
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Name" name="dependencies[][name]" />
        </div>
        <div class="col-md">
            <select class="form-control" name="dependencies[][type]">
                <option selected>Select Type</option>
                <option value="javascript">Javascript</option>
                <option value="css">Css</option>
            </select>
        </div>
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Path" name="dependencies[][path]" />
        </div>
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Version" name="dependencies[][version]" />
        </div>
        <div class="col-md-1">
            <span class="js-remove-cloned-item text-danger" style="font-size: 20px;">&times;</span>
        </div>
    </div>
</div>

<div class="js-hidden-form-fields-option d-none">
    <div class="row mb-2 js-form-fields-option">
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Field Name" name="form-fields[][name]" />
        </div>
        <div class="col-md">
            <select class="form-control" name="form-fields[][type]">
                <option selected>Select Field Type</option>
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="file">File</option>
            </select>
        </div>
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Default Value" name="form-fields[][default_value]" />
        </div>
        <div class="col-md-1">
            <span class="js-remove-form-fields-cloned-item text-danger" style="font-size: 20px;">&times;</span>
        </div>
    </div>
</div>

@endsection
@section('js_scripts')
<script>
    $(document).ready(function() {
        let dependencyIndex = 0;

        // Function to clone a dependency row
        function cloneDependency(firstItem = false) {
            var clonedDependencyItem = $('.js-hidden-dependency-option .js-dependency-option').clone().removeClass('d-none').addClass('js-cloned-item');
            if (firstItem) {
                var clonedDependencyItem = $('.js-hidden-dependency-option .js-dependency-option').clone().removeClass('d-none').addClass(firstItem);
            }

            clonedDependencyItem.find('[name="dependencies[][name]"]').attr('name', 'dependencies[' + dependencyIndex + '][name]');
            clonedDependencyItem.find('[name="dependencies[][type]"]').attr('name', 'dependencies[' + dependencyIndex + '][type]');
            clonedDependencyItem.find('[name="dependencies[][path]"]').attr('name', 'dependencies[' + dependencyIndex + '][path]');
            clonedDependencyItem.find('[name="dependencies[][version]"]').attr('name', 'dependencies[' + dependencyIndex + '][version]');

            $('.dependencies-container').append(clonedDependencyItem);
            dependencyIndex++;
        }

        cloneDependency('first-cloned-item');

        $('.js-add-dependency').click(function() {
            cloneDependency();
        });

        $('body').on('click', '.js-remove-cloned-item', function() {
            $(this).closest('.js-dependency-option.js-cloned-item').remove()
        });


        let formFieldIndex = 0;

    function cloneFormField() {
        var clonedFormFieldItem = $('.js-hidden-form-fields-option .js-form-fields-option').clone().removeClass('d-none').addClass('js-cloned-item');

        clonedFormFieldItem.find('[name="form-fields[][name]"]').attr('name', 'form-fields[' + formFieldIndex + '][name]');
        clonedFormFieldItem.find('[name="form-fields[][type]"]').attr('name', 'form-fields[' + formFieldIndex + '][type]');
        clonedFormFieldItem.find('[name="form-fields[][default_value]"]').attr('name', 'form-fields[' + formFieldIndex + '][default_value]');

        $('.form-fields-container').append(clonedFormFieldItem);
        formFieldIndex++;
    }

    cloneFormField();

    $('.js-add-form-fields').click(function () {
        cloneFormField();
    });

    $('body').on('click', '.js-remove-form-fields-cloned-item', function() {
            $(this).closest('.js-form-fields-option').remove()
        });

    });
</script>
@endsection
