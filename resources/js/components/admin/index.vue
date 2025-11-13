<template>
    <div>
        <!-- <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Admin System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-2">
                        <a class="nav-link" href="#">Profile</a>
                    </li>
                    <li class="nav-item d-flex">
                        <div class="btn-logout">
                            <a class="nav-link d-flex align-items-center gap-2" href="#">
                                <span>Log Out</span>
                                <div class="circle-wrapper">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
                </div>
            </div>
        </nav> -->

        <div class="sidebar bg-light m-5">
            <ul class="nav flex-column">
                <!-- <li class="nav-item">
                    <router-link to="/api/index" class="nav-link text-dark" active-class="active">Dashboard</router-link>
                </li> -->
                <li class="nav-item">
                    <router-link to="/api/company" class="nav-link text-dark" active-class="active">Company</router-link>
                </li>
                <li class="nav-item">
                <router-link to="/api/book" class="nav-link text-dark" active-class="active">Book</router-link>
                </li>
                <li class="nav-item">
                <router-link to="/api/rack" class="nav-link text-dark" active-class="active">Book Rack</router-link>
                </li>
                <li class="nav-item">
                <router-link to="/api/record" class="nav-link text-dark" active-class="active">Record</router-link>
                </li>
                <!-- Add more items as needed -->
            </ul>
        </div>

       

        <!-- <input type="text" v-model="form."> -->



        <!-- <router-link to="/api/register" class="text-decoration-none">Login here</router-link> -->
        
        <!-- <router-link to="/test">Back to Home</router-link> -->
    </div>
</template>