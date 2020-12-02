import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            redirect: '/user'
        },
        {
            path: '/login',
            component: () => import('../components/views/user/login.vue'),
            meta: { title: '登录' }
        },
        {
            path: '*',
            redirect: '/404'
        },
        {
            path: '/',
            component: () => import('../components/common/Home.vue'),
            meta: { title: '后台管理系统' },
            children: [
                {
                    path: '/user',
                    component: () => import('../components/views/user/list.vue'),
                    meta: { title: '用户管理' }
                },
                {
                    path: '/useradd',
                    component: () => import('../components/views/user/add.vue'),
                    meta: { title: '添加用户' }
                },
                {
                    path: '/role',
                    component: () => import('../components/views/role/list.vue'),
                    meta: { title: '用户管理' }
                },
                {
                    path: '/roleadd',
                    component: () => import('../components/views/role/add.vue'),
                    meta: { title: '添加用户' }
                },
                {
                    path: '/permission',
                    component: () => import('../components/views/permission/list.vue'),
                    meta: { title: '用户管理' }
                },
                {
                    path: '/permissionadd',
                    component: () => import('../components/views/permission/add.vue'),
                    meta: { title: '添加用户' }
                },
                {
                    path: '/brand',
                    component: () => import('../components/views/brand/list.vue'),
                    meta: { title: '品牌管理' }
                },
                {
                    path: '/brandAdd',
                    component: () => import('../components/views/brand/add.vue'),
                    meta: { title: '添加品牌' }
                },
                {
                    path: '/goodsType',
                    component: () => import('../components/views/goodsType/list.vue'),
                    meta: { title: '商品分类管理' }
                },
                {
                    path: '/goodsTypeAdd',
                    component: () => import('../components/views/goodsType/add.vue'),
                    meta: { title: '添加商品分类' }
                },
                {
                    path: '/goods',
                    component: () => import('../components/views/goods/list.vue'),
                    meta: { title: '商品管理' }
                },
                {
                    path: '/goodsAdd',
                    component: () => import('../components/views/goods/add.vue'),
                    meta: { title: '添加商品' }
                },
                {
                    path: '/order',
                    component: () => import('../components/views/order/list.vue'),
                    meta: { title: '订单管理' }
                },
                {
                    path: '/coupon',
                    component: ()=> import('../components/views/coupon/list.vue'),
                    meta: { title: '优惠券管理' }
                },
                {
                    path: '/couponAdd',
                    component: () => import('../components/views/coupon/add.vue'),
                    meta: { title: '添加优惠券' }
                },
                {
                    path: '/404',
                    component: () => import('../components/common/404.vue'),
                    meta: { title: '404' }
                },
                {
                    path: '/403',
                    component: () => import('../components/common/403.vue'),
                    meta: { title: '403' }
                }
            ]
        }
    ]
});
