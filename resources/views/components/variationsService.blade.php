@php
    /** @var \App\Models\Produto $produto */
@endphp

@each('components.servicoVariacoes', $service->variations()->with('service.attrAttrs', 'variations', 'calendars')->get(), 'variationUpdateModel')
