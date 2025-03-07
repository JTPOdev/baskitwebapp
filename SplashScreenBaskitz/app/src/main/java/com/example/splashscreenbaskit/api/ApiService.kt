package com.example.splashscreenbaskit.api

import LoginRequest
import LoginResponse
import RegisterRequest
import RegisterResponse
import StoreRequestResponse
import StoreResponse
import UserResponse
import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.Body
import retrofit2.http.POST
import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Multipart
import retrofit2.http.Part

interface ApiService {

    @POST("/user/register")
    suspend fun register(@Body registerData: RegisterRequest): Response<RegisterResponse>

    @POST("/user/login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @POST("/user/logout")
    suspend fun logout(@Header("Authorization") token: String): Response<Void>

    @GET("user/details")
    suspend fun getUserDetails(@Header("Authorization") token: String): Response<UserResponse>


    @Multipart
    @POST("store/create")
    suspend fun sendRequest(
        @Part("user_id") userId: RequestBody,
        @Part("store_name") storeName: RequestBody,
        @Part("owner_name") ownerName: RequestBody,
        @Part("store_phone_number") storePhone: RequestBody,
        @Part("store_address") storeAddress: RequestBody,
        @Part("store_origin") storeOrigin: RequestBody,
        @Part("registered_store_name") registeredStoreName: RequestBody,
        @Part("registered_store_address") registeredStoreAddress: RequestBody,
        @Part("store_status") storeStatus: RequestBody,
        @Part certificate: MultipartBody.Part?,
        @Part validId: MultipartBody.Part?
    ): Response<StoreRequestResponse>

    @GET("store/all")
    suspend fun getStoreDetails(@Header("Authorization") authorization: String): Response<List<StoreResponse>>
}
