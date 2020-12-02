<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-plus"></i> 添加权限
        </el-breadcrumb-item>
        <el-breadcrumb-item>添加权限</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="form-box">
        <el-form ref="form" :model="form" :rules="rules" label-width="100px">
          <el-form-item label="权限名称" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item label="上级权限名称">
            <el-select v-model="form.parent_id" clearable filterable placeholder="请选择">
              <el-option
                v-for="item in menuList"
                :key="item.value"
                :label="item.label"
                :value="item.value"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="权限路径">
            <el-input v-model="form.path"></el-input>
          </el-form-item>
          <el-form-item label="排序" prop="sort_num">
            <el-input type='number' v-model="form.sort_num"></el-input>
          </el-form-item>
          <el-form-item label="类型" prop="type">
            <el-select v-model="form.type" clearable placeholder="请选择">
              <el-option
                v-for="item in typeList"
                :key="item.value"
                :label="item.label"
                :value="item.value"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="权限图标">
            <el-input v-model="form.icon"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">提交</el-button>
            <el-button>取消</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
import { permissionAdd, permissionMenu } from '@/request/api'
export default {
  data() {
    return {
      form: {
        name: '',
        path: '',
        type: '',
        icon: '',
        parent_id: '',
        sort_num: ''
      },
      menuList: [],
      rules:{
        name:[
          {required:true,message:'权限名称不能为空',trigger:'blur'},
          {min:1,max:50,message:'长度在1到64个字符之间',trigger:'blur'}
        ],
        sort_num:[
          {required:true,message:'排序不能为空',trigger:'blur'},
        ],
        type:[
          {required:true,message:'类型不能为空',trigger:'change'},
        ]
      },
      typeList:[
        {'label':'菜单','value':1},
        {'label':'操作','value':2}
      ]
    }
  },
   created() {
    this.getMenu();
  },
  methods: {
    getMenu() {
      permissionMenu().then(res => {
        if (res.code == 200) {
          this.menuList = res.data
        } else {
          console.log('menulist:',res.msg)
        }
      }).catch((error) => {
        console.log('getMenuError:',error)
      })
    },
    onSubmit() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          permissionAdd(this.form).then(res=>{
            if (res.code==200) {
              this.$router.push('/permission')
            } else {
              this.$message.error(res.msg)
            }
          })
        }
      })
    }
  }
}
</script>