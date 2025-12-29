<table class="table table-bordered datatable" id="shopTable">
    <thead>
        <tr>
            <th class="text-left" scope="col">ID</th>
            <th class="text-left" scope="col">Name</th>
            <th class="text-left" scope="col">Phone Number</th>
            <th class="text-left" scope="col">Location</th>
            <th class="text-right" scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shops as $shop)
            <tr>
                <td class="text-left">{{$shop->id}}</td>
                <td class="text-left">{{$shop->name}}</td>
                <td class="text-left">{{$shop->primaryPhone}}</td>
                <td class="text-left">{{$shop->address}}</td>
                <td class="text-right"> 
                    <button data-shopID="{{$shop->id}}" class='btn thm-btn-bg thm-btn-text-color show-shop'><i class='fa-solid fa-eye'></i></button>
                    <button data-shopID="{{$shop->id}}" class='btn thm-btn-bg thm-btn-text-color edit-shop'><i class='fa-solid fa-pen-to-square'></i></button>
                    <button data-shopID="{{$shop->id}}" class='btn thm-btn-bg thm-btn-text-color delete-shop'><i class='fa-solid fa-trash'></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
