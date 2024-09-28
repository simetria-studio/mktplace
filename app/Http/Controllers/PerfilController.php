<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;

use App\Mail\CodeInfo;
use App\Mail\OrderCancel;

use App\Models\SignedPlan;

use App\Models\AffiliatePs;
use App\Models\StarProduct;
use App\Models\StarService;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\AffiliateInfo;
use App\Models\AffiliateItem;
use App\Models\CustomerAddress;
use App\Models\OrderRequestCancel;
use Illuminate\Support\Facades\Mail;

class PerfilController extends Controller
{
    public function perfilSave(Request $request)
    {
        User::find(auth()->user()->id)->update([
            'name'  => $request->name,
            'cpf'   => $request->cpf,
            'birth_date' => $request->birth_date ? date('Y-m-d', strtotime(str_replace('/','-', $request->birth_date))) : null,
        ]);

        return redirect('/perfil')->with('success', 'Seus dados foram atualizados com successo!');
    }

    public function senhaSave(Request $request)
    {
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            $auth = User::find(auth()->user()->id);
        
            return $auth && Hash::check($value, $auth->password);
        });

        $request->validate([
            'current_password' => ['required', 'string', 'min:8', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $auth = User::find(auth()->user()->id);

        $auth->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('perfil')->with('success', 'Senha alterada com sucesso!');
    }

    public function enderecoSave(Request $request)
    {
        $request->validate([
            'post_code' => 'required',
            'state'     => 'required',
            'city'      => 'required',
            'address'   => 'required',
            'address2'  => 'required',
            'number'    => 'required',
            'phone2'    => 'required',
        ]);

        $addresses['user_id']       = auth()->user()->id;
        $addresses['post_code']     = $request->post_code;
        $addresses['state']         = $request->state;
        $addresses['city']          = $request->city;
        $addresses['address2']      = $request->address2;
        $addresses['address']       = $request->address;
        $addresses['number']        = $request->number;
        $addresses['complement']    = $request->complement;
        $addresses['phone1']        = $request->phone1;
        $addresses['phone2']        = $request->phone2;

        if($request->id){
            CustomerAddress::where('id', $request->id)->update($addresses);
            return redirect()->back()->with('success', 'Endereço Atualizado!');
        }else{
            CustomerAddress::create($addresses);
            return redirect()->back()->with('success', 'Endereço Salvo!');
        }
    }

    public function apagarEndereco($id)
    {
        $address = CustomerAddress::find($id);

        $address->delete();

        return redirect('perfil')->with('destroy', 'Endereço Excluido!');
    }

    public function rateProduct(Request $request)
    {
        \Log::info(['perfilcontroller - rateProduct',$request->all()]);
        StarProduct::create([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'star' => $request->estrela,
            'comment' => $request->comment ?? '',
        ]);

        return response()->json($request->all(), 200);
    }

    public function rateService(Request $request)
    {
        StarService::create([
            'user_id' => auth()->user()->id,
            'service_id' => $request->service_id,
            'star' => $request->estrela,
            'comment' => $request->comment ?? '',
        ]);

        return response()->json($request->all(), 200);
    }

    public function envCodeDelete()
    {
        $code_delete = \Str::random(8);
        User::find(auth()->guard('web')->user()->id)->update(['code_delete' => $code_delete]);

        Mail::to(auth()->guard('web')->user()->email)->send(new CodeInfo(User::find(auth()->guard('web')->user()->id), 'env_code'));

        return response()->json();
    }

    public function confirmCodeDelete(Request $request)
    {
        if(User::where('id', auth()->guard('web')->user()->id)->where('code_delete', $request->code_delete)->count() == 0){
            return response()->json(['error_msg' => 'Codigo invalido!'],412);
        }

        $user = User::find(auth()->guard('web')->user()->id);

        User::find($user->id)->delete();
        CustomerAddress::where('user_id', $user->id)->delete();

        Mail::to($user->email)->send(new CodeInfo($user, 'delete_account'));

        return response()->json();
    }

    public function solicitaCancelamentoPedido(Request $request)
    {
        $order = Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();
        Order::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->update(['pay' => 10]);

        $orderCancel['order_number']        = $request->order_number;
        $orderCancel['title']               = $request->title;
        $orderCancel['reason']              = $request->reason;
        $orderCancel['bank_code_id']        = $request->bank_code_id ?? null;
        $orderCancel['agencia']             = $request->agencia ?? null;
        $orderCancel['agencia_dv_id']       = $request->agencia_dv_id ?? null;
        $orderCancel['conta_id']            = $request->conta_id ?? null;
        $orderCancel['conta_dv_id']         = $request->conta_dv_id ?? null;
        $orderCancel['type']                = $request->type ?? null;
        $orderCancel['document_number_id']  = $request->document_number_id ?? null;
        $orderCancel['legal_name_id']       = $request->legal_name_id ?? null;
        if(OrderRequestCancel::where('order_number', $request->order_number)->get()->count() == 0){
            OrderRequestCancel::create($orderCancel);
        }else{
            OrderRequestCancel::where('order_number', $request->order_number)->update($orderCancel);
        }

        Mail::to('comercial@raeasy.com')->send(new OrderCancel($order, $request->reason));
        if($order->pay == 10){
            return response()->json(['msg_10' => 'Solicitação ja feita, aguarde ate que respondam!']);
        }else{
            return response()->json(['msg_10' => 'Sua solicitação ja foi anotada, aguarde ate que respondam!']);
        }
    }

    public function solicitaCancelamentoPedidoService(Request $request)
    {
        $order = OrderService::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->first();
        OrderService::where('user_id', auth()->user()->id)->where('order_number', $request->order_number)->update(['pay' => 10]);

        $orderCancel['order_number']        = 'S-'.$request->order_number;
        $orderCancel['title']               = $request->title;
        $orderCancel['reason']              = $request->reason;
        $orderCancel['bank_code_id']        = $request->bank_code_id ?? null;
        $orderCancel['agencia']             = $request->agencia ?? null;
        $orderCancel['agencia_dv_id']       = $request->agencia_dv_id ?? null;
        $orderCancel['conta_id']            = $request->conta_id ?? null;
        $orderCancel['conta_dv_id']         = $request->conta_dv_id ?? null;
        $orderCancel['type']                = $request->type ?? null;
        $orderCancel['document_number_id']  = $request->document_number_id ?? null;
        $orderCancel['legal_name_id']       = $request->legal_name_id ?? null;
        if(OrderRequestCancel::where('order_number', 'S-'.$request->order_number)->get()->count() == 0){
            OrderRequestCancel::create($orderCancel);
        }else{
            OrderRequestCancel::where('order_number', 'S-'.$request->order_number)->update($orderCancel);
        }

        Mail::to('comercial@raeasy.com')->send(new OrderCancel($order, $request->reason));
        if($order->pay == 10){
            return response()->json(['msg_10' => 'Solicitação ja feita, aguarde ate que respondam!']);
        }else{
            return response()->json(['msg_10' => 'Sua solicitação ja foi anotada, aguarde ate que respondam!']);
        }
    }

    public function solicitaCancelamentoAssinatura(Request $request)
    {
        $order = SignedPlan::find($request->signedplan_id ?? $request->order_number);
        if(isset($request->signedplan_id)){
            SignedPlan::find($request->signedplan_id ?? $request->order_number)->update(['status' => 2]);
            \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('subscriptions', '/'.$order->pagarme_id), [])->object();
            \Http::withHeaders(get_header_conf_pm())->delete(url_pagarme('charges', '/'.$order->sub_signed_plan->first()->cobranca_id), [])->object();
        }else{
            SignedPlan::find($request->signedplan_id ?? $request->order_number)->update(['status' => 4]);
            $orderCancel['order_number']        = 'ASS-'.$request->order_number;
            $orderCancel['title']               = $request->title ?? 'Cancelamento de Assinatura';
            $orderCancel['reason']              = $request->reason;
            $orderCancel['bank_code_id']        = $request->bank_code_id ?? null;
            $orderCancel['agencia']             = $request->agencia ?? null;
            $orderCancel['agencia_dv_id']       = $request->agencia_dv_id ?? null;
            $orderCancel['conta_id']            = $request->conta_id ?? null;
            $orderCancel['conta_dv_id']         = $request->conta_dv_id ?? null;
            $orderCancel['type']                = $request->type ?? null;
            $orderCancel['document_number_id']  = $request->document_number_id ?? null;
            $orderCancel['legal_name_id']       = $request->legal_name_id ?? null;
            if(OrderRequestCancel::where('order_number', 'ASS-'.$request->order_number)->get()->count() == 0){
                OrderRequestCancel::create($orderCancel);
            }else{
                OrderRequestCancel::where('order_number', 'ASS-'.$request->order_number)->update($orderCancel);
            }
        }

        // Mail::to('comercial@raeasy.com')->send(new OrderCancel($order, $request->reason ?? 'Assinatura cancelada instantaneamente'));
        if($order->pay == 10){
            return response()->json(['msg_10' => 'Solicitação ja feita, aguarde ate que respondam!']);
        }else{
            if(isset($request->signedplan_id)) return response()->json(['msg_10' => 'Assinatura cancelada com sucesso!']);
            return response()->json(['msg_10' => 'Sua solicitação ja foi anotada, aguarde ate que respondam!']);
        }
    }

    public function salvarAfiliado(Request $request)
    {
        $request->validate([
            'bank_code'       => 'required',
            'agencia'         => 'required',
            'conta'           => 'required',
            'conta_dv'        => 'required',
            'type'            => 'required',
            'document_number' => 'required',
            'legal_name'      => 'required',
        ]);

        switch ($request->type) {
            case 'conta_corrente':
                $type = "checking";
                $holder_type = "individual";
            break;
            case 'conta_poupanca':
                $type = "savings";
                $holder_type = "individual";
            break;
            case 'conta_corrente_conjunta':
                $type = "checking";
                $holder_type = "company";
            break;
            case 'conta_poupanca_conjunta':
                $type = "savings";
                $holder_type = "company";
            break;
            default:
            break;
        }

        $affiliate['user_id']             = $request->user_id;
        $affiliate['bank']                = $request->bank_code;
        $affiliate['branch_number']       = $request->agencia;
        $affiliate['branch_check_digit']  = $request->agencia_dv;
        $affiliate['account_number']      = $request->conta;
        $affiliate['account_check_digit'] = $request->conta_dv;
        $affiliate['type']                = $type;
        $affiliate['holder_document']     = $request->document_number;
        $affiliate['holder_name']         = $request->legal_name;
        $affiliate['holder_type']         = $holder_type;
        $affiliate['status']              = 0;

        if($request->id){
            $affiliate['status'] = 1;
            AffiliateInfo::where('id', $request->id)->update($affiliate);

            $recebedor = AffiliateInfo::where('id', $request->id)->with(['users'])->first();

            $collectSendRecipients = collect([
                // message: "ERROR TYPE: invalid_parameter. PARAMETER: external_id. MESSAGE: External ID must be unique"
                'name' => $recebedor->users->name,
                'email' => $recebedor->users->email,
                'document' => $request->get('document_number'),
                'type' => in_array($request->get('type'),account_types_pagarme()) ? 'individual' : 'company',
                // 'external_id' => md5($seller->id.$request->get('document_number')),
    
                // de acordo com reunião realizada no dia 30/09/2021
                // a regra de transferência é no primeiro dia útil do mês
                'transfer_settings' => [
                    "transfer_enabled" => false,
                    "transfer_interval" => 'monthly',
                    "transfer_day" => 1,
                ],
    
                "automatic_anticipation_settings" => [
                    "enabled" => false,
                    "type" => "full",
                    "volume_percentage" => "50",
                    "delay" => null
                ],
    
                'default_bank_account' => [
                    'bank' => $request->get('bank_code'),
                    'branch_number' => $request->get('agencia'),
                    'branch_check_digit' => empty($request->get('agencia_dv')) ? 0 : $request->get('agencia_dv'),
                    'account_number' => $request->get('conta'),
                    'account_check_digit' => $request->get('conta_dv'),
                    'type' => in_array($request->get('type'),account_types_pagarme()) ? 'checking' : 'savings',
                    'holder_document' => $request->get('document_number'),
                    'holder_name' => $request->get('legal_name'),
                    'holder_type' => in_array($request->get('type'),account_types_pagarme()) ? 'individual' : 'company',
                ]
            ]);

            if(empty($recebedor->wallet_id)){
                $recebedor_pagarme = \Http::withHeaders(get_header_conf_pm())->post(url_pagarme('recipients'), $collectSendRecipients->toArray())->object();
                \Log::info(collect($recebedor_pagarme));
                AffiliateInfo::where('id', $request->id)->update(['wallet_id' => $recebedor_pagarme->id]);
            }
            else{
                $recebedor_pagarme = \Http::withHeaders(get_header_conf_pm())->patch(url_pagarme('recipients', '/'.$recebedor->wallet_id).'/default-bank-account', ['bank_account' => $collectSendRecipients['default_bank_account']])->object();
                \Log::info(collect($recebedor_pagarme));
            }

            return redirect()->back()->with('success', 'Dados Bancários Atualizados!');
        }else{
            AffiliateInfo::create($affiliate);
            return redirect()->back()->with('success', 'Dados Bancários Salvos!');
        }
    }

    public function excluirAfiliado(Request $request)
    {
        AffiliateInfo::where('id', $request->id)->delete();

        return redirect()->back()->with('success', 'Afiliado excluído com sucesso!');
    }

    public function salvarLinkAfiliado(Request $request)
    {
        $affiliate = AffiliateInfo::with('users')->find($request->affiliate_id);
        $affiliate_item = AffiliateItem::with('product', 'service')->find($request->affiliate_item);

        $user_code = explode(' ', $affiliate->users->name);

        $create_affiliate_ps['reference_id'] = $request->reference_id;
        $create_affiliate_ps['affiliate_id'] = $request->affiliate_id;
        $create_affiliate_ps['affiliate_item'] = $request->affiliate_item;
        $create_affiliate_ps['codigo'] = mb_convert_case($user_code[0][0], MB_CASE_UPPER).(count($user_code) > 0 ? mb_convert_case($user_code[count($user_code)-1][0], MB_CASE_UPPER) : '').'-'.($affiliate_item->reference_type == 'product' ? 'P-' : 'S-').$affiliate->users->id;
        $create_affiliate_ps['url'] = ($affiliate_item->reference_type == 'product' ? route('product',[$affiliate_item->product->slug, $create_affiliate_ps['codigo']]) : route('service', [$affiliate_item->service->service_slug, $create_affiliate_ps['codigo']]));
        $create_affiliate_ps['status'] = 1;

        AffiliatePs::create($create_affiliate_ps);

        return response()->json($create_affiliate_ps['url']);
        /* 
            - Recebe os IDS
            - Verifica se eh produto ou serviço
            - Com o ID do produto/serviço, pega o slug original
            - Com o ID do usuario, pega a primeira letra do nome+sobrenome, e o ID
            - Monta o link com o slug original + /NS-ID

            - Cria rota
            - Salva na AffiliatePs
        */
    }
}
