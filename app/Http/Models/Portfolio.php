<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
	protected $table = 'portfolio';
	public $timestamps = true;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 'stock_symbol', 'stock_qty', 'stock_buy_price','updated_at'
	];
}