<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Customer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','address', 'phone'
    ];

    // Relations //
    public function order_customers(){
        return $this->hasMany('App\Models\OrderCustomer');
    }
    public function customerGallons(){
        return $this->hasMany('App\Models\CustomerGallon');
    }

    public function get($id){
        $customer = Customer::with([
            'customerGallons' => function($query){
                $query->orderBy('type', 'DESC');
            },
            'order_customers' => function($query){
                $query->with([
                    'order' => function($query){
                        $query->with(['user', 'issues']);
                    },
                ]);
                $query->has('order');
                $query->orderBy('delivery_at', 'DESC');
                $query->orderBy('id', 'DESC');
            }])
            ->find($id);

        if($customer->customerGallons->count() > 0 && $customer->order_customers->count() > 0){
            $last_transaction = Carbon::parse($customer->order_customers[0]->delivery_at);
            if(!$customer->notif_day){
                $notif_day = 14;
            }
            else{
                $notif_day = $customer->notif_day;
            }
            $overdue = $last_transaction->addDay($notif_day);

            $customer->overdue = Carbon::now()->diffInDays($overdue, false);
            $customer->last_transaction = $customer->order_customers[0]->delivery_at;
            $customer->overdue_date = $overdue->format('Y-m-d');
        }
        else{
            $customer->overdue = null;
            $customer->last_transaction = null;
            $customer->overdue_date = null;
        }

        return $customer;
    }

    public function getOverdueCustomers(){
        $customers = Customer::with([
            'customerGallons' => function($query){
                $query->orderBy('type', 'DESC');
            },
            'order_customers' => function($query){
                $query->with([
                    'order' => function($query){
                        $query->with(['user', 'issues']);
                    },
                ]);
                $query->has('order');
                $query->orderBy('delivery_at', 'DESC');
                $query->orderBy('id', 'DESC');
            }])
            ->has('order_customers')
            ->get();

        $res = collect();

        foreach($customers as $key => $val){
            $last_transaction = Carbon::parse($customers[$key]->order_customers[0]->delivery_at);
            if(!$customers[$key]->notif_day){
                $notif_day = 14;
            }
            else{
                $notif_day = $customers[$key]->notif_day;
            }
            $overdue = $last_transaction->addDay($notif_day);

            $customers[$key]->overdue = Carbon::now()->diffInDays($overdue, false);
            $customers[$key]->last_transaction = $customers[$key]->order_customers[0]->delivery_at;
            $customers[$key]->overdue_date = $overdue->format('Y-m-d');

            if($customers[$key]->overdue <= 0){
                $res->push($customers[$key]);
            }
        };

        return $res;
    }

    public function doMake($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        $this->notif_day = $data->notif_day;
        if($data->type){
            $this->type = 'agent';
        }else{
            $this->type = 'end_customer';
        }
        
        $this->save();        

        return $this;
    }

    public function doUpdate($data)
    {
        $this->name = $data->name;
        $this->address = $data->address;
        $this->phone = $data->phone;
        $this->type = $data->type;
        $this->notif_day = $data->notif_day;

        return ($this->save());
    }

    public function doRestore(){
        return $this->restore();
    }

    public function doDelete(){
        return $this->delete();
    }

    public function doForceDelete(){
        return $this->forceDelete();
    }
}
