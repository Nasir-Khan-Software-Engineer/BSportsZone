<table class="table table-bordered datatable" id="unitTable">
    <thead>
        <tr>
            <th class="text-center align-middle" scope="col">ID</th>
            <th class="text-center align-middle" scope="col">Name</th>
            <th class="text-center align-middle" scope="col">Short Form</th>
            <th class="text-center align-middle" scope="col">Created On</th>
            <th class="text-center align-middle" scope="col">Created By</th>
            <th class="text-center align-middle" scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($units as $unit)
            <tr>
                <td class="text-center align-middle">{{$unit->id}}</td>
                <td class="text-center align-middle">{{$unit->name}}</td>
                <td class="text-center align-middle">{{$unit->shortform }}</td>
                <td class="text-center align-middle">
                    <div class="text-center d-inline-block px-2" style="line-height: normal;">
                        {{ $unit->formattedTime }}
                        <br>
                        {{ $unit->formattedDate }}
                    </div>
                </td>
                <td class="text-center align-middle">{{$unit->createdBy}}</td>
                <td class="text-center align-middle"> 
                    <button data-unitID="{{$unit->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color edit-unit'><i class='fa-solid fa-pen-to-square'></i></button>
                    <button data-unitID="{{$unit->id}}" class='btn btn-sm thm-btn-bg thm-btn-text-color delete-unit'><i class='fa-solid fa-trash'></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>