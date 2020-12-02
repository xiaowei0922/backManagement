<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/" v-if="form.id == 0">
        <el-breadcrumb-item>
          <i class="el-icon-plus"></i> 添加商品
        </el-breadcrumb-item>
        <el-breadcrumb-item>添加商品</el-breadcrumb-item>
      </el-breadcrumb>
      <el-breadcrumb separator="/" v-else>
        <el-breadcrumb-item>
          <i class="el-icon-plus"></i> 编辑商品
        </el-breadcrumb-item>
        <el-breadcrumb-item>编辑商品</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="form-box">
        <el-form ref="form" :model="form" :rules="rules" label-width="80px">
          <el-form-item label="商品名称" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item label="状态" prop="status">
            <el-select v-model="form.status" placeholder="请选择">
              <el-option
                v-for="item in statelist"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="类型" prop="status">
            <el-select v-model="form.status" placeholder="请选择">
              <el-option
                v-for="item in statelist"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="商品价格" prop="price">
            <el-input v-model="form.price"></el-input>
          </el-form-item>
          <el-form-item label="商品简介" prop="decription">
            <el-input type="textarea" v-model="form.decription"></el-input>
          </el-form-item>
          <el-form-item label="商品详情" prop="content">
            <quill-editor ref="text" v-model="form.content" class="myQuillEditor" :options="editorOption" />
          </el-form-item>
          <el-form-item label="商品图片" :prop="form.id==0 ? 'picture' : ''">
            <el-upload
              :action="uploadAction"
              list-type="picture-card"
              ref="upload"
              name="picture"
              accept="image/png,image/jpg,image/gif,image/jpeg"
              :on-success="handleAvatarSuccess"
              :before-upload="beforeAvatarUpload"
              :file-list="fileList"
              :on-change="filechange"
              :data="form"
              :auto-upload="false"
              :multiple="true" :limit=6
              :on-preview="handlePictureCardPreview">
              <i class="el-icon-plus"></i>
              <div slot="tip" class="el-upload__tip">选择.jpg、.png、.gif、。jpeg格式文件且大小不能超过2M</div>
            </el-upload>
            <el-dialog :visible.sync="dialogVisible">
              <img width="100%" :src="dialogImageUrl" alt="">
            </el-dialog>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">表单提交</el-button>
            <el-button>取消</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>
<script>
import { userDetail, userAdd, roleIdName } from '@/request/api'
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
export default {
  data() {
    var password = (rule, value, callback) => {
      this.$refs.form.validateField('repassword');
      callback();
    }
    var repassword = (rule, value, callback) => {
      if (value !== this.form.password) {
        callback(new Error('两次输入密码不一致!'));
      } else {
        callback();
      }
    }
    var mobile = (rule,value,callback)=>{
      if(!/^1[3456789]\d{9}$/.test(value)){
        callback(new Error('请输入正确的手机号'))
      }else{
        callback()
      }
    }
    var email=(rule,value,callback)=>{
      if (!/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/.test(value)) {
        callback(new Error('请输入正确的邮箱'))
      }else{
        callback()
      }
    }
    return {
      form: {
        id: '',
        user_name: '',
        mobile: '',
        email: '',
        status: '',
        password:'',
        repassword:'',
        role_id: '',
        avatar:''
      },
      roleIdNameList:[],
      rules: {
        name:[
          {required:true, message: '请输入商品名称', trigger: 'blur'},
          {min:3,max:30,message:'长度在3到30个字符之间',trigger:'blur'}
        ],
        password:[
          {required:true,message:'请输入密码',trigger:'blur'},
          {validator:password,trigger:'blur'},
        ],
        repassword:[
          {required:true,message:'请再次输入密码',trigger:'blur'},
          {validator:repassword,trigger:'blur'},
        ],
        email:[
          {required:true,message:'请输入邮箱',trigger:'blur'},
          {validator:email,trigger:'blur'},
        ],
        mobile:[
          {required:true,message:'请输入手机号',trigger:'blur'},
          {validator:mobile,trigger:'blur'},
        ],
        status:[
          {required:true,message:'请输入状态',trigger:'blur'},
        ],
        role_id:[
          {required:true,message:'请选择角色',trigger:'blur'},
        ],
        avatar:[
          {required:true,message:'请上传头像',trigger:'blur'},
        ]
      },
    statelist:[
      {'label':'正常','value':1},
      {'label':'禁用','value':2}
    ],
    dialogImageUrl: '',
      dialogVisible: false,
      upload_flag:false,
      uploadAction:domain+'/admin/users/saveUser',
      fileList:[]
    }
  },
  created() {
    this.getDetail()
    this.getRoleIdNameList()
  },
  components: {
    quillEditor
  },
  methods: {
    filechange(file){
      if (file.name) {
        this.upload_flag=true;
        this.form.avatar=true;
        const isLt2M = file.size / 1024 /1024 < 2;
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        return isLt2M;
      }
    },
    getDetail(){
      var uid = this.$route.query.id;
      if (uid) {
        this.form.id = uid
        userDetail({id:uid}).then(res => {
          this.form=res
          this.fileList=[{name:'头像',url:domain+res.avatar}]
        }).catch((error) => {
          console.log(error)
        })
      }
    },
    getRoleIdNameList(){
       roleIdName().then(res => {
          if (res.code == 200) {
            this.roleIdNameList=res.data
          }
        }).catch((error) => {
          console.log(error)
        })
    },
    saveSuccess(res){
      if (res.error_code == 200) {
        this.$router.push('/user')
      } else {
        this.$message.error(res.msg)
      }
    },
    handleAvatarSuccess(res) {
      this.saveSuccess(res)
    },
    beforeAvatarUpload(file) {
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isLt2M) {
        this.$message.error('上传头像图片大小不能超过 2MB!');
      }
      return isLt2M;
    },
    onSubmit() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          if(this.upload_flag){
            this.$refs.upload.submit();
          } else {
            userAdd(this.form).then(res => {
              this.saveSuccess(res)
            }).catch((error) => {
              console.log(error)
            })
          }
        }
      })
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
  }
};
</script>