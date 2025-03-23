<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Shipping;
use App\User;
use PDF;
use Notification;
use Helper;
use Auth;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders=Order::orderBy('id','DESC')->paginate(10);
        return view('backend.order.index')->with('orders',$orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'first_name'=>'string|required',
            'last_name'=>'string|required',
            'address1'=>'string|required',
            'address2'=>'string|nullable',
            'coupon'=>'nullable|numeric',
            'phone'=>'numeric|required',
            'post_code'=>'string|nullable',
            'email'=>'string|required'
        ]);

        if(Auth::check()){
            if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
                request()->session()->flash('error','Cart is Empty !');
                return back();
            }

            $user = $request->user()->id;
        }
        else{
            if(empty(Cart::where('user_id',0)->where('order_id',null)->first())){
                request()->session()->flash('error','Cart is Empty !');
                return back();
            }

            $user = 0;
        }

        $order=new Order();
        $order_data=$request->all();
        $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']=$user;
        $order_data['shipping_id']=$request->shipping;
        $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');
        // return session('coupon')['value'];
        $order_data['sub_total']=Helper::totalCartPrice();
        $order_data['quantity']=Helper::cartCount();
        if(session('coupon')){
            $order_data['coupon']=session('coupon')['value'];
        } 

        if(session('coupon')){
            $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
        }
        else{
            $order_data['total_amount']=Helper::totalCartPrice();
        }

        // return $order_data['total_amount'];
        $order_data['status']="new";
        $order_data['payment_method']=$request->payment_method;
        // dd($order_data);
        $order->fill($order_data);
        $status=$order->save();

        if($order)
        // dd($order->id);
        $users = User::where('role','admin')->first();
        $details =[
            'title'=>'New order created',
            'actionURL'=>route('order.show',$order->id),
            'fas'=>'fa-file-alt'
        ];
        Notification::send($users, new StatusNotification($details));
        if (request('payment_method') == 'ipaymu') {
            return $this->paymentWithIpaymu($order);
        } elseif (request('payment_method') == 'doku') {
            return $this->paymentWithDoku($order);
        } else {
            session()->forget('cart');
            session()->forget('coupon');
        }
        

        if (Auth::check()) {
            Cart::where('user_id', auth()->user()->id)->where('order_id', null)->delete();
        } else {
            Cart::where('user_id', 0)->where('order_id', null)->delete();
        }


        // dd($users);        
        request()->session()->flash('success','Your product successfully placed in order');
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::find($id);
        // return $order;
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $order=Order::find($id);
        return view('backend.order.edit')->with('order',$order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order=Order::find($id);
        $this->validate($request,[
            'status'=>'required|in:new,process,delivered,cancel'
        ]);
        $data=$request->all();
        // return $request->status;
        if($request->status=='delivered'){
            foreach($order->cart as $cart){
                $product=$cart->product;
                // return $product;
                $product->stock -=$cart->quantity;
                $product->save();
            }
        }
        $status=$order->fill($data)->save();
        if($status){
            request()->session()->flash('success','Successfully updated order');
        }
        else{
            request()->session()->flash('error','Error while updating order');
        }
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            $status=$order->delete();
            if($status){
                request()->session()->flash('success','Order Successfully deleted');
            }
            else{
                request()->session()->flash('error','Order can not deleted');
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        // return $request->all();
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success','Your order has been placed. please wait.');
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success','Your order is under processing please wait.');
                return redirect()->route('home');
    
            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success','Your order is successfully delivered.');
                return redirect()->route('home');
    
            }
            else{
                request()->session()->flash('error','Your order canceled. please try again');
                return redirect()->route('home');
    
            }
        }
        else{
            request()->session()->flash('error','Invalid order numer please try again');
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request){
        $order=Order::getAllOrder($request->id);
        // return $order;
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        // return $file_name;
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }
    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        // dd($year);
        $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('payment_status','paid')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
            // dd($items);
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->sum('total_amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }

    public function paymentWithIpaymu($order)
    {

        if(Auth::check()){
            $items = Cart::where('user_id', auth()->user()->id)
                    ->get();
        }
        else{
            $items = Cart::where('user_id', 0)
                    ->get();
        }

        $productItems = [];
        foreach ($items as $item) {
            $productItems[] = [
                'name' => $item->product->title,
                'price' => $item->price,
                'quantity' => $item->quantity
            ];
        }

        $payload = [
            'product' => array_column($productItems, 'name'),
            'price' => array_column($productItems, 'price'),
            'qty' => array_column($productItems, 'quantity'),
            'returnUrl' => route('payment.status.ipaymu', ['order_id' => $order->id]),
            'notifyUrl' => route('payment.status.ipaymu', ['order_id' => $order->id]),
            'buyerName' => $order->first_name . '' . $order->last_name,
            'buyerEmail' => $order->email,
            'buyerPhone' => $order->phone,
            'referenceId' => $order->order_number,
        ];

        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $requestBody = strtolower(hash('sha256', $jsonBody));
        $stringToSign = strtoupper('POST') . ':' . env('IPAYMU_VA') . ':' . $requestBody . ':' . env('IPAYMU_API_KEY');
        $signature = hash_hmac('sha256', $stringToSign, env('IPAYMU_API_KEY'));
        $timestamp = Date('YmdHis');

        $curl = curl_init(env('IPAYMU_API_URL'));
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'va: ' . env('IPAYMU_VA'),
            'signature: ' . $signature,
            'timestamp: ' . $timestamp
        ];

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonBody);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);

        if ($response && isset($response->Success) && $response->Success) {
            $reference = $response->Data->Url;
            return redirect($reference);
        } else {
            $message = $response->Message ?? 'Gagal terhubung dengan iPaymu.';
            return back()->withErrors($message);
        }
    }

    public function paymentWithDoku($order)
    {
        $targetPath = "/credit-card/v1/payment-page"; 
        $clientId = env('DOKU_CLIENT_ID');
        $secretKey = env('DOKU_SECRET_KEY');
        $requestId = uniqid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $requestBody = [
            "order" => [
                "invoice_number" => $order->order_number,
                "amount" => $order->total_amount,
                "currency" => "IDR",
                "callback_url" => route('order.success', ['order_id' => $order->id]),
                "auto_redirect"=> false,
            ],
            "customer" => [
                "id" => (string) $order->user_id,
                "name" => $order->first_name . ' ' . $order->last_name,
                "email" => $order->email,
            ],
        ];

        $digestValue = base64_encode(hash('sha256', json_encode($requestBody), true));

        $componentSignature = "Client-Id:" . $clientId . "\n" .
                            "Request-Id:" . $requestId . "\n" .
                            "Request-Timestamp:" . $timestamp . "\n" .
                            "Request-Target:" . $targetPath . "\n" .
                            "Digest:" . $digestValue;

        $signature = base64_encode(hash_hmac('sha256', $componentSignature, $secretKey, true));

        $headers = [
            "Content-Type: application/json",
            "Client-Id: $clientId",
            "Request-Id: $requestId",
            "Request-Timestamp: $timestamp",
            "Signature: HMACSHA256=$signature"
        ];

        $ch = curl_init(env('DOKU_API_URL') . $targetPath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return back()->withErrors("Gagal terhubung ke DOKU: $error");
        }

        $responseData = json_decode($response);

        if (isset($responseData->credit_card_payment_page->url)) {
            return redirect($responseData->credit_card_payment_page->url);
        } else {
            return back()->withErrors("Gagal mendapatkan URL pembayaran dari DOKU.");
        }
    }



    public function paymentSuccess(Request $request)
    {

        $order_id = $request->query('order_id');
        $status = $request->input('status');
        $order = Order::find($order_id);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        if ($status == 'berhasil') {
            $order->payment_status = 'paid';
            $order->save();
            session()->forget('cart');
            session()->forget('coupon');

            if (Auth::check()) {
                Cart::where('user_id', auth()->user()->id)->where('order_id', null)->delete();
            } else {
                Cart::where('user_id', 0)->where('order_id', null)->delete();
            }

            request()->session()->flash('success', 'Pembayaran berhasil!');
            return redirect()->route('home');
        } else {
            $order->payment_status = 'unpaid';
            $order->save();
            request()->session()->flash('error', 'Pesanan tidak ditemukan.');

            if (Auth::check()) {
                Cart::where('user_id', auth()->user()->id)->where('order_id', null)->delete();
            } else {
                Cart::where('user_id', 0)->where('order_id', null)->delete();
            }
            
            return redirect()->route('home');
        }

    }

    public function success(Request $request)
    {
        Log::info('DOKU Callback Received: ', $request->all());


        $data = $request->all();
        $orderNumber = $data['order']['invoice_number'] ?? null;
        $transactionStatus = $data['transaction']['status'] ?? null;

        if (!$orderNumber) {
            return response()->json(['message' => 'Invalid callback data'], 400);
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus === 'SUCCESS') {
            $order->status = 'paid';
        } elseif ($transactionStatus === 'FAILED') {
            $order->status = 'failed';
        } elseif ($transactionStatus === 'PENDING') {
            $order->status = 'pending';
        }

        $order->save();

        return response()->json(['message' => 'Callback processed successfully']);
    }

}
