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
                        @if ($errors->has('category'))
                             <span style="font-size: 12px;" class="text-danger">{{ $errors->first('category') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-5">
                    <label for="preview" class="col-sm-3 col-form-label required">Preview Image</label>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" name="preview" id="preview">
                        @if ($errors->has('preview'))
                             <span style="font-size: 12px; " class="text-danger">{{ $errors->first('preview') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-5 mt-4">
                    <div class="col-md-3">
                        <label class="required">Dependency:</label>
                    </div>
                </div>
                <div class="dependencies-container">
                @if ($errors->has('dependencies.*'))
                    @foreach($errors->get('dependencies.*') as $key => $errorMessages)
                    <span style="font-size: 12px; padding-left:15px;" class="text-danger">
                    @foreach($errorMessages as $error)
                        @if ($error == 'The dependencies.0.name field is required.')
                            Name Field is required in Dependency.
                        @elseif ($error == 'The dependencies.0.path field is required.')
                                Path Field is required in Dependency.
                        @elseif ($error == 'The dependencies.0.version field is required.')
                                    Versioin is required in  Dependency.
                        @else
                        {{$error}}
                        @endif
                    </span>
                    @endforeach
                    @endforeach
                    @endif
                </div>
                <span class="js-add-dependency clone text-success" style="font-size: 20px;">+</span>

                <div class="row mb-5 mt-4">
                    <div class="col-md-3">
                        <label  class="required" >Form Fields:</label>
                    </div>
                </div>

                <div class="form-fields-container">

                @if ($errors->has('form-fields.*'))
                    @foreach($errors->get('form-fields.*') as $key => $errorMessages)
                    <span style="font-size: 12px; padding: 10px 100px;" class="text-danger">
                    @foreach($errorMessages as $error)
                        @if ($error == 'The form-fields.0.name field is required.')
                            Name field is required in Form Field.
                        @elseif ($error == 'The form-fields.0.default_value field is required.')
                            default value is required in Form Feild.
                        @else
                        {{$error}}
                        @endif
                    @endforeach
                    </span>
                    @endforeach
                    @endif
                </div>
                <!-- add button for new form field in component -->
                <span class="js-add-form-fields clone text-success" style="font-size: 20px;">+</span>

                <!-- status for the Component -->
                <div class="row mb-5">
                    <label for="status" class="col-sm-3 col-form-label required ">Status</label>
                    <div class="col-sm-4">
                        <select name="status" class="form-select" id="status">
                            <option value="draft">Draft</option>
                            <option value="testing">Testing</option>
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>
                        </select>
                        @if ($errors->has('status'))
                             <span style="font-size: 12px;" class="text-danger">{{ $errors->first('status') }}</span>
                        @endif
                    </div>
                </div>


                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- code for clone new row for dependency -->
<div class="js-hidden-dependency-option d-none">
    <div class="row mb-2 js-dependency-option">
        <div class="col-md">
            <input type="text" class="form-control" placeholder="Name" name="dependencies[][name]" />
        </div>
        <div class="col-md">
            <select class="form-control" name="dependencies[][type]">
                <option selected>Select Type</option>
                <option value="js">Javascript</option>
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

<!-- code for clone new row for form fields -->

<div class="js-hidden-form-fields-option d-none">
    <div class="overflow-auto">
        <div class="row mb-2 js-form-fields-option">
            <!-- Your existing fields -->
            <div class="col-md">
                <input type="text" class="form-control" placeholder="Field Name" name="form-fields[][name]" />
            </div>
            <div class="col-md">
                <select class="form-control selectFieldType" name="form-fields[][type]" id="fieldType">
                    <option selected>Select Field Type</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="textarea">TextArea</option>
                    <option value="button">Button</option>
                    <option value="multiple_list">Multiple List</option>
                </select>
            </div>
            <div class="col-md">
                <input type="text" class="form-control" placeholder="Field Position" name="form-fields[][field_position]" />
            </div>
            <div class="col-md-3 defaultValueContainer">
                <input type="text" class="form-control formDefaultValue" placeholder="Default Value" name="form-fields[][default_value]" value="" />
                <input type="file" class="form-control imageUploadValue imageFilePath" name="form-fields[][default_image][]" style="display: none;" onchange="updateDefaultValue(this)" />
                <label for="multipleImageUpload" class="js-multiple-image-upload imageUploadValue" style="display: none;">multiple</label>
                <input type="checkbox" id="multipleImageUpload" name="form-fields[][multiple_image]" class="js-multiple-image-upload imageUploadValue" style="margin-top: 5px; display: none;">
            </div>
            <div class="col-md">
                <input type="text" class="form-control" placeholder="Meta Key1 (Optional)" name="form-fields[][meta_key1]" />
            </div>
            <div class="col-md">
                <input type="text" class="form-control" placeholder="Meta Key2 (Optional)" name="form-fields[][meta_key2]" />
            </div>
            <div class="col-md-1">
                <span class="js-remove-form-fields-cloned-item text-danger" style="font-size: 20px;">&times;</span>
            </div>
        </div>
    </div>
</div>


@endsection
@section('js_scripts')
<script>
        $(document).ready(function () {
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

            $('.js-add-dependency').click(function () {
                cloneDependency();
            });

            $('body').on('click', '.js-remove-cloned-item', function () {
                $(this).closest('.js-dependency-option.js-cloned-item').remove();
            });


            let formFieldIndex = 0;

            function cloneFormField() {
                var clonedFormFieldItem = $('.js-hidden-form-fields-option .js-form-fields-option').clone().removeClass('d-none').addClass('js-cloned-item');

                clonedFormFieldItem.find('[name="form-fields[][name]"]').attr('name', 'form-fields[' + formFieldIndex + '][name]');
                clonedFormFieldItem.find('[name="form-fields[][type]"]').attr('name', 'form-fields[' + formFieldIndex + '][type]');
                clonedFormFieldItem.find('[name="form-fields[][field_position]"]').attr('name', 'form-fields[' + formFieldIndex + '][field_position]');
                clonedFormFieldItem.find('[name="form-fields[][default_value]"]').attr('name', 'form-fields[' + formFieldIndex + '][default_value]');
                clonedFormFieldItem.find('[name="form-fields[][default_image][]"]').attr('name', 'form-fields[' + formFieldIndex + '][default_image][]');
                clonedFormFieldItem.find('[name="form-fields[][multiple_image]"]').attr('name', 'form-fields[' + formFieldIndex + '][multiple_image]');
                clonedFormFieldItem.find('[name="form-fields[][meta_key1]"]').attr('name', 'form-fields[' + formFieldIndex + '][meta_key1]');
                clonedFormFieldItem.find('[name="form-fields[][meta_key2]"]').attr('name', 'form-fields[' + formFieldIndex + '][meta_key2]');

                $('.form-fields-container').append(clonedFormFieldItem);
                console.log("before",formFieldIndex);
                formFieldIndex++;
                console.log("after",formFieldIndex);

            }

            cloneFormField();

            $('.js-add-form-fields').click(function () {
                cloneFormField();
            });

            $('body').on('click', '.js-remove-form-fields-cloned-item', function () {
                $(this).closest('.js-form-fields-option').remove();
            });

            $(document).on('change', '.selectFieldType', function () {
                const selectedValue = $(this).val();
                const closestParent = $(this).closest('.js-form-fields-option');
                const defaultValueInput = closestParent.find('.formDefaultValue');
                const imageUpload = closestParent.find('.imageUploadValue');
                const defaultValueContainer = closestParent.find('.defaultValueContainer');

                if (selectedValue === 'image') {
                    defaultValueInput.hide();
                    imageUpload.show();
                } else {
                    defaultValueInput.show();
                    imageUpload.hide();
                }
            });

            $(document).on('change', '#multipleImageUpload', function () {
                console.log("change function",formFieldIndex);
                const isChecked = $(this).prop('checked');
                const imageUpload = $(this).closest('.js-form-fields-option').find('.imageFilePath');

                if (isChecked) {
                    // If checkbox is checked, show multiple file input
                    imageUpload.attr('multiple', 'multiple');
                    // imageUpload.attr('name', 'form-fields['+ formFieldIndex +'][default_image][]'); // Add [] to the name for array
                } else {
                    // If checkbox is unchecked, hide multiple file input
                    imageUpload.removeAttr('multiple');
                    // imageUpload.attr('name', 'form-fields['+ formFieldIndex +'][default_image]'); // Remove [] from the name
                }
            });

        });

        function updateDefaultValue(input) {
            var file = input.files[0];
            var fileName = file.name;
            const closestParent = $(input).closest('.defaultValueContainer');
            const insertDefaultValue = closestParent.find('.formDefaultValue').val(fileName);
            // console.log(closestParent.find('.formDefaultValue').val());
        }

</script>
@endsection
