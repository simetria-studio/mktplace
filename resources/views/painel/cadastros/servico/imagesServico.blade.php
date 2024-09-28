<div class="m-2 position-relative" data-id="{{$image->id}}">
    <button type="button" class="btn btn-danger btn-sm btn-delete-foto" data-id="{{$image->id}}" data-url="{{\Request::url()}}?postService=true&postType=postDeleteFoto">X</button>
    <div class="mb-3 p-2 div-image @if($image->principal == 1) active @endif">
        <input type="radio" name="img_principal" class="d-none" value="{{$image->id}}" @if($image->principal == 1) checked @endif>
        <div class="div-image-bg" style="background-image: url('{{$image->caminho}}')"></div>
        <span>PRINCIPAL</span>
    </div>
    <div class="form-group">
        <input type="text" class="form-control form-control-sm" name="fotos[{{$image->id}}][legenda]" value="{{$image->legenda ?? ''}}" placeholder="Adicione uma legenda">
    </div>
    <div class="form-group">
        <input type="text" class="form-control form-control-sm" name="fotos[{{$image->id}}][texto_alternativo]" value="{{$image->texto_alternativo ?? ''}}" placeholder="Adicione um texto alt">
    </div>
</div>