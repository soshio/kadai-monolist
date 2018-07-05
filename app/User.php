<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    
    //want　と Have の設定
  public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }

    public function want_items()
    {
        return $this->items()->where('type', 'want');
    }
    
     public function have_items()
    {
        return $this->items()->where('type', 'have');
    }


    //wantメソッド
    public function want($itemId)
    {
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemId);

        if ($exist) {
            // 既に Want していれば何もしない
            return false;
        } else {
            // 未 Want であれば Want する
            $this->items()->attach($itemId, ['type' => 'want']);
            return true;
        }
    }

    //wantを外すメソッド
    public function dont_want($itemId)
    {
        // 既に Want しているかの確認
        $exist = $this->is_wanting($itemId);

        if ($exist) {
            // 既に Want していれば Want を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [\Auth::id(), $itemId]);
            return true;
        } else {
            // 未 Want であれば何もしない
            return false;
        }
    }
    
     //haveメソッド
    public function have($itemId)
    {
        // 既に have しているかの確認
        $exist = $this->is_having($itemId);

        if ($exist) {
            // 既に have していれば何もしない
            return false;
        } else {
            // 未 have であれば have する
            $this->items()->attach($itemId, ['type' => 'have']);
            return true;
        }
    }

    //haveを外すメソッド
    public function dont_have($itemId)
    {
        // 既に have しているかの確認
        $exist = $this->is_wanting($itemId);

        if ($exist) {
            // 既に have していれば have を外す
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [\Auth::id(), $itemId]);
            return true;
        } else {
            // 未 Want であれば何もしない
            return false;
        }
    }
    
    
    //既にwantしているか確認のためのメソッド
    public function is_wanting($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->want_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    //既にhaveしているか確認のためのメソッド
     public function is_having($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
}
