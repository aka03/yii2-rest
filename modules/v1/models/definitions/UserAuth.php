<?php

namespace app\models\definitions;

/**
 * @OA\Schema(required={"first_name", "last_name", "patronymic_name", "login", "email", "password"},
 *      @OA\Property(property="first_name", type="string"),
 *      @OA\Property(property="last_name", type="string"),
 *      @OA\Property(property="patronymic_name", type="string"),
 *      @OA\Property(property="login", type="string"),
 *      @OA\Property(property="password", type="string"),
 *      @OA\Property(property="email", type="string"),
 * )
 */
class UserAuth
{
}
