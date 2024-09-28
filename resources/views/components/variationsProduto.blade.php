@php
    /** @var \App\Models\Produto $produto */
@endphp

@each('components.produtoVariacoes', $produto->variations()->with('produto.attrAttrs', 'variations')->get(), 'variationUpdateModel')
