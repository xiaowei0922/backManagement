<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-plus"></i> 添加品牌
        </el-breadcrumb-item>
        <el-breadcrumb-item>添加品牌</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="form-box">
        <el-form ref="form" :model="form" :rules="rules" label-width="80px">
          <el-form-item label="品牌名称" prop="name">
            <el-input v-model="form.name"></el-input>
          </el-form-item>
          <el-form-item label="排序" prop="sort_num">
            <el-input type='number' v-model="form.sort_num"></el-input>
          </el-form-item>
          <el-form-item label="状态" prop="status">
            <el-select v-model="form.status" clearable placeholder="请选择">
              <el-option
                v-for="item in statusList"
                :key="item.value"
                :label="item.label"
                :value="item.value"></el-option>
            </el-select>
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
import { roleAdd, permissionTree, roleDetail } from '@/request/api'
export default {
  data() {
    return {
      form: {
        name: '',
        status: '',
        sort_num: ''
      },
      permissionList:[],
      rules:{
        name:[
          {required:true,message:'品牌名称不能为空',trigger:'blur'},
          {min:1,max:50,message:'长度在1到50个字符之间',trigger:'blur'}
        ],
        sort_num:[
          {required:true,message:'排序不能为空',trigger:'blur'},
        ],
        status:[
          {required:true,message:'状态不能为空',trigger:'change'},
        ]
      },
      statusList:[
        {'label':'正常','value':1},
        {'label':'禁用','value':2}
      ]
    }
  },
  created() {
    this.getPermissionList()
    this.getView()
    this.permissions=JSON.parse(localStorage.getItem('permissions'))
  },
  methods: {
    onSubmit() {
      this.form.permIdList=this.$refs.tree.getCheckedKeys();
      this.$refs['form'].validate((valid) => {
        if (valid) {
          this.$delete(this.form,'create_time')
          this.$delete(this.form,'update_time')
          roleAdd(this.form).then(res=>{
            if (res.code == 200) {
              this.$router.push('/role')
            } else {
              this.$message.error(res.msg)
            }
          })
        }
      })
    },
    getView(){
      roleDetail({id:this.$route.query.id}).then(res => {
        if (res.code == 200) {
          this.form = res.data.roleInfo
          this.$refs.tree.setCheckedKeys(res.data.permIdList);
        } else {
          this.$message.error(res.msg)
        }
      }).catch((error)=>{
        console.log(error)
      })
    },
    getPermissionList() {
      permissionTree().then(res => {
        if (res.code == 200) {
          this.permissionList=res.data
        } else {
          this.$message.error(res.msg)
        }
      })
    }
  }
}
</script>