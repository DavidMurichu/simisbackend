<?php

namespace App\Models;
use App\Models\AuthBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'view_all',
        'username',
        'password',
        'zoneid',
        'password_reset_code',
        'password_reset_time',
        'salt',
        'activation_code',
        'forgotten_password_code',
        'forgotten_password_time',
        'remember_code',
        'created_on',
        'last_login',
        'first_name',
        'last_name',
        'company',
        'phone',
        'levelid',
        'employeeid',
        'memberid',
        'branch_id',
        'email',
        'active',
        'ipaddress',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $this->load('authBranch');
        $branchName = $this->authBranch ? $this->authBranch->branch_name : null;
        return [
            'username' => $this->username,
            'branch_name'=>$branchName,
            'active' => $this->active ? 'active' : 'inactive',
            'user_id' => $this->id,
        ];
    }
    public function authBranch()
    {
        return $this->belongsTo(AuthBranch::class,'branch_id');
    }
    public function get_fillable(){
        $restricted=[  'password',
        'password_reset_code',
        'password_reset_time',
        'salt',
        'activation_code',
        'forgotten_password_code',
        'forgotten_password_time',
        'remember_code',
        'created_on',
        'last_login',];
        $columns=array_diff($this->fillable, $restricted);
        return $columns;
    }
}
