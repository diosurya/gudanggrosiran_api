<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
<body>
    <div class="container my-5">
        <form action="" method="POST">
            @csrf
            <div class="row mb-3">
                <label for="id_user" class="col-md-4 col-form-label text-md-end">Akun Pengirim</label>
                <div class="col-md-6">
                    <select class="form-control" name="id_pengirim">
                        <option selected disabled>Select Akun</option>
                        @foreach($akun as $value)
                            <option value="{{ $value->id }}">{{ $value->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="id_user" class="col-md-4 col-form-label text-md-end">Akun Penerima</label>
                <div class="col-md-6">
                    <select class="form-control" name="id_penerima">
                        <option selected disabled>Select User</option>
                        @foreach($akun as $value)
                            <option value="{{ $value->id }}">{{ $value->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="id_user" class="col-md-4 col-form-label text-md-end">saldo</label>
                <div class="col-md-6">
                <input type="number" class="form-control" name="jumlah_saldo" value="">
                </div>
            </div>
            <button class="btn btn-success" type="submit">Transfer</button>
        </form>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>