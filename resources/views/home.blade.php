 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Upload image') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                      <form method="POST" enctype="multipart/form-data" action="{{url('/uploadimg')}}">
                           @csrf
                           <table class="table table-bordered">
                        <thead>
                        <tr>                            
                        <th scope="col">Image</th>
                        <th scope="col">Pdf</th>
                         
                        </tr>
                        </thead>
                        <tbody>
                             <tr>
                        <th scope="row"><input type="file" name="img[]" value="imag"multiple></th>
                        <td><input type="submit" name="submit" value="Submit"></td>
       
    </tr>
                          

                      </form>
                </div>
            </div>
        </div>
    </div>
</div>
 
