import { createRouter, createWebHistory } from 'vue-router'
import Index from '../components/admin/index.vue'
import test from '../components/admin/test.vue'

import AdminLogin from '../components/admin/login.vue'
import AdminRegister from '../components/admin/register.vue'
import AdminLayout from '../components/admin/layout.vue'
import AdminDashboard from '../components/admin/dashboard.vue'

// company
import Company from '../components/admin/company/index.vue';
import CreateCompany from '../components/admin/company/create.vue';
import EditCompany from '../components/admin/company/edit.vue';

// job
import Job from '../components/admin/job/index.vue';
import CreateJob from '../components/admin/job/create.vue';
import EditJob from '../components/admin/job/edit.vue';

// application
import Application from '../components/admin/application/index.vue';

// interview
import Interview from '../components/admin/interview/index.vue';

// User
import User from '../components/admin/user/index.vue';

import Dashboard from '../components/admin/dashboard.vue';


// User
import Register from '../components/user/register.vue'
import Login from '../components/user/login.vue'

import UserLayout from '../components/user/layout.vue'
import Home from '../components/user/home.vue'
import JobDetail from '../components/user/jobDetail.vue'
import Profile from '../components/user/profile.vue'
import ApplyJob from '../components/user/apply.vue'
import ApplicationHistory from '../components/user/applicationHistory.vue'

const adminRoutes = [
    { 
        path: '/', 
        name: 'AdminLogin', 
        component: AdminLogin
    },
    { 
        path: '/register', 
        name: 'AdminRegister', 
        component: AdminRegister
    },
    {
        path: '/admin',
        name: 'AdminLayout',
        component: AdminLayout,
        children: [
            {
                path: '', // Default path
                name: 'Dashboard',
                component: AdminDashboard,
            },
            {
                path: 'company',
                name: 'Company',
                component: Company
            },
            {
                path: 'company/create',
                name: 'CreateCompany',
                component: CreateCompany
            },
            {
                path: 'company/:id/edit',
                name: 'EditCompany',
                component: EditCompany,
                props: true
            },
            {
                path: 'job',
                name: 'Job',
                component: Job
            },
            {
                path: 'job/create',
                name: 'CreateJob',
                component: CreateJob
            },
            {
                path: 'job/:id/edit',
                name: 'EditJob',
                component: EditJob,
                props: true
            },
            {
                path: 'application',
                name: 'Application',
                component: Application
            },
            {
                path: 'interview',
                name: 'Interview',
                component: Interview
            },
            {
                path: 'user',
                name: 'User',
                component: User
            },
        ]
    },
];

const userRoutes = [
    { 
        path: '/user/login', 
        name: 'Login', 
        component: Login
    },
    { 
        path: '/user/register', 
        name: 'Register', 
        component: Register
    },
    { 
        path: '/user/home', 
        component: UserLayout,
        children: [
            { 
                path: '', 
                name: 'Home', 
                component: Home
            },
            { 
                path: '/user/jobDetail/:id', 
                name: 'JobDetail', 
                component: JobDetail
            },
            { 
                path: '/user/profile', 
                name: 'Profile', 
                component: Profile
            },
            { 
                path: '/user/:id/apply', 
                name: 'ApplyJob', 
                component: ApplyJob,
                props: true
            },
             { 
                path: '/user/applicationHistory', 
                name: 'ApplicationHistory', 
                component: ApplicationHistory
            },
        ],
    },
];

const routes = [
    ...adminRoutes,
    ...userRoutes,
];

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;
