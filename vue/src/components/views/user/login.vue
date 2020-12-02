<template>
  <div class="login-wrap">
    <div class="ms-login">
      <div class="ms-title">后台管理系统</div>
      <el-form :model="param" :rules="rules" ref="login" label-width="0px" class="ms-content">
        <el-form-item prop="user_name">
          <el-input v-model="param.user_name" placeholder="用户名">
            <el-button slot="prepend" icon="el-icon-user"></el-button>
          </el-input>
        </el-form-item>
        <el-form-item prop="password">
          <el-input
            type="password"
            placeholder="password"
            v-model="param.password"
          >
            <el-button slot="prepend" icon="el-icon-lock"></el-button>
          </el-input>
        </el-form-item>
        <el-form-item prop="captcha">
          <el-input v-model="param.captcha" placeholder="验证码">
            <el-button slot="prepend" icon="el-icon-document"></el-button>
          </el-input>
        </el-form-item>
        <img @click="proxyImage" :src="captchasrc" alt="" style="height:50px;width:100%">
        <div class="login-btn">
          <el-button type="primary" @click="submitForm()">登录</el-button>
        </div>
        <p class="login-tips">Tips : 用户名和密码随便填。</p>
      </el-form>
    </div>
  </div>
</template>

<script>
import { login } from '@/request/api'
export default {
  data: function() {
    return {
      param: {
        user_name: 'admin',
        password: '123123',
        captcha: '',
      },
      captchasrc:"/admin/index/verify",
      rules: {
        user_name: [{ required: true, message: '请输入用户名', trigger: 'blur' }],
        password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
        captcha: [{ required: true, message: '请输入验证码', trigger: 'blur' }],
      },
    };
  },
  methods: {
    proxyImage() {
      this.captchasrc="/admin/index/verify?rand="+Math.random()
    },
    submitForm() {
      this.$refs.login.validate(valid => {
        if (valid) {
          login(this.param).then(res => {
            if (res.code == 200) {
              this.$message.success('登录成功');
              localStorage.setItem('userInfo', JSON.stringify(res.data.user));
              this.$router.push('/');
              localStorage.setItem('permissions',JSON.stringify(res.data.permissions))
            } else {
              this.$message.error(res.msg)
            }
          })
        } else {
          return false;
        }
      });
    },
  },
};
</script>

<style scoped>
.login-wrap {
    position: relative;
    width: 100%;
    height: 100%;
    background-image: url(../../../assets/img/login-bg.jpg);
    background-size: 100%;
}
.ms-title {
    width: 100%;
    line-height: 50px;
    text-align: center;
    font-size: 20px;
    color: #fff;
    border-bottom: 1px solid #ddd;
}
.ms-login {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 350px;
    margin: -190px 0 0 -175px;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.3);
    overflow: hidden;
}
.ms-content {
    padding: 30px 30px;
}
.login-btn {
    text-align: center;
}
.login-btn button {
    width: 100%;
    height: 36px;
    margin-bottom: 10px;
}
.login-tips {
    font-size: 12px;
    line-height: 30px;
    color: #fff;
}
</style>