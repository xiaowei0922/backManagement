<template>
    <div>
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>
                    <i class="el-icon-lx-cascades"></i> 用户管理
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="container">
            <div class="handle-box">
                <router-link :to="{path:'useradd'}" v-if="permissions.indexOf('/admin/users/saveUser')!=-1"><el-button
                    type="primary"
                    icon="el-icon-delete"
                    class="handle-del mr10"
                >添加用户</el-button></router-link>
                <el-select v-model="query.status" placeholder="用户状态" class="mr10">
                  <el-option
                    v-for="item in statelist"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                  </el-option>
                </el-select>
                 <el-select v-model="query.role_id" placeholder="角色" class="mr10">
                  <el-option
                    v-for="item in roleIdNameList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                  </el-option>
                </el-select>
                <el-input v-model="query.user_name" placeholder="用户名" class="handle-input mr10"></el-input>
                <el-input v-model="query.mobile" placeholder="手机号" class="handle-input mr10"></el-input>
                <div class="block">
                  <el-date-picker
                    v-model="rangeTime"
                    type="datetimerange"
                    value-format="yyyy-MM-dd HH:mm:ss"
                    :unlink-panels="false"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                  </el-date-picker>
                </div>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch">搜索</el-button>
            </div>
            <el-table
              :data="tableData"
              border
              class="table"
              header-cell-class-name="table-header"
            >
              <el-table-column prop="id" label="ID" width="55" align="center"></el-table-column>
              <el-table-column prop="user_name" label="用户名"></el-table-column>
              <el-table-column label="角色">
                <template slot-scope='scope'>
                    {{roleIdNameList[scope.row.role_id].name}}
                </template>
              </el-table-column>
              <el-table-column prop="mobile" label="手机号"></el-table-column>
              <el-table-column prop="email" label="邮箱"></el-table-column>
              <el-table-column label="头像(查看大图)" align="center">
                <template slot-scope="scope">
                  <el-image
                    class="table-td-thumb"
                    :src="scope.row.avatar"
                    :preview-src-list="['http://tp5.local/'+scope.row.avatar]"
                  ></el-image>
                </template>
              </el-table-column>
              <el-table-column label="状态" align="center">
                <template slot-scope="scope">
                  <el-tag :type="scope.row.status == 1 ? 'success' : 'danger'">{{scope.row.status== 1 ? '正常' : '禁用'}}</el-tag>
                </template>
              </el-table-column>
              <el-table-column prop="create_time" label="添加时间"></el-table-column>
              <el-table-column prop="update_time" label="更新时间"></el-table-column>
              <el-table-column label="操作" width="180" align="center">
                  <template slot-scope="scope">
                    <router-link :to="{path:'useradd',query:{'id':scope.row.id}}">
                      <el-button
                        v-if="permissions.indexOf('/admin/users/saveUser')!=-1"
                        type="text"
                        icon="el-icon-edit"
                      >编辑</el-button>
                    </router-link>
                  <el-button
                      v-if="permissions.indexOf('/admin/users/delUser')!=-1"
                      type="text"
                      icon="el-icon-delete"
                      class="red"
                      @click="handleDelete(scope.$index, scope.row)"
                  >删除</el-button>
                </template>
              </el-table-column>
            </el-table>
            <div class="pagination">
              <el-pagination
                background
                layout="total, prev, pager, next"
                :current-page="query.page"
                :page-size="query.pageSize"
                :total="pageTotal"
                @current-change="handlePageChange"
              ></el-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import { userList,userDel } from '@/request/api'
export default {
  name: 'basetable',
  data() {
    return {
      permissions:[],
      query: {
        status: '',
        user_name: '',
        mobile:'',
        role_id:'',
        pageIndex: 1,
        startTime:'',
        endTime:'',
        pageSize: 2
      },
      statelist:[
        {'label':'正常','value':1},
        {'label':'禁用','value':2}
      ],
      roleIdNameList:[],
      tableData: [],
      pageTotal: 0,
      rangeTime: []
    }
  },
  created() {
    this.getData();
    this.permissions = JSON.parse(localStorage.getItem('permissions'));
  },
  methods: {
    // 获取 easy-mock 的模拟数据
    getData() {
      if (this.rangeTime.length>0) {
        this.query.startTime=this.rangeTime[0]
        this.query.endTime=this.rangeTime[1]
      }
      userList(this.query).then(res => {
        this.tableData = res.data.userList
        this.pageTotal = res.total
        this.roleIdNameList=res.data.roleIdNameList
      }).catch((error) => {
        console.log(error)
      })
    },
    // 触发搜索按钮
    handleSearch() {
      this.$set(this.query, 'pageIndex', 1);
      this.getData();
    },
    // 删除操作
    handleDelete(index, row) {
      // 二次确认删除
      this.$confirm('确定要删除吗？', '提示', {
        type: 'warning'
      })
      .then(() => {
        userDel({id:row.id}).then(res=>{
          if (res.error_code == 200) {
            this.$message.success('删除成功');
            this.tableData.splice(index, 1);
          } else {
            this.$message.error(res.msg);
          }
        }).catch((error)=>{
          console.log(error)
        })
      })
      .catch((error) => {
        console.log(error)
      });
    },
    // 分页导航
    handlePageChange(val) {
      this.$set(this.query, 'pageIndex', val);
      this.getData();
    }
  }
};
</script>

<style scoped>
.handle-box {
    margin-bottom: 20px;
}

.handle-select {
    width: 120px;
}

.handle-input {
    width: 150px;
    display: inline-block;
}
.table {
    width: 100%;
    font-size: 14px;
}
.red {
    color: #ff0000;
}
.mr10 {
    margin-right: 10px;
}
.table-td-thumb {
    display: block;
    margin: auto;
    width: 40px;
    height: 40px;
}
.block{
  margin: 10px;
  width: 400px;
  display: inline-block;
}
</style>
