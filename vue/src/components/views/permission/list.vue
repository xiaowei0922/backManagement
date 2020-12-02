<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
            <i class="el-icon-plus"></i> 权限管理
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <router-link
          to="/permissionadd"
          v-if="permissions.indexOf('/admin/permissions/savePermission') != -1"
        >
          <el-button
            type="primary"
            icon="el-icon-delete"
            class="handle-del mr10"
          >添加权限</el-button>
        </router-link>
        <el-input v-model="query.name" placeholder="名称" class="handle-input mr10"></el-input>
        <el-select v-model="query.status" clearable placeholder="类型" class="handle-select mr10">
          <el-option
            v-for="item in typeList"
            :key="item.value"
            :label="item.label"
            :value="item.value"></el-option>
        </el-select>
        <el-select v-model="query.parent_id" clearable filterable placeholder="父级名称" class="handle-select mr10">
          <el-option
            v-for="item in menuList"
            :key="item.value"
            :label="item.label"
            :value="item.value"></el-option>
        </el-select>
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
        <el-table-column prop="name" label="权限名称"></el-table-column>
        <el-table-column label="父级名称">
          <template slot-scope="scope">
            <span style="margin-left: 10px">{{ getParentName(scope.row.parent_id) }}</span>
          </template>
        </el-table-column>
        <el-table-column label="类型" align="center">
          <template slot-scope="scope">
            <el-tag
              :type="scope.row.type==1?'success':'danger'"
              >{{scope.row.type==1?'菜单':'操作'}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="path" label="操作路径"></el-table-column>
        <el-table-column prop="icon" label="菜单图标"></el-table-column>
        <el-table-column prop="sort_num" label="排序"></el-table-column>
        <el-table-column prop="create_time" label="添加时间"></el-table-column>
        <el-table-column label="操作" width="180" align="center">
          <template slot-scope="scope">
            <el-button
                v-if="permissions.indexOf('/admin/permissions/savePermission') != -1 && permissions.indexOf('/admin/permissions/getMenuList') != -1 && permissions.indexOf('/admin/permissions/getPermissionList') != -1"
                type="text"
                icon="el-icon-edit"
                @click="handleEdit(scope.$index, scope.row)"
            >编辑</el-button>
            <el-button
                v-if="permissions.indexOf('/admin/permissions/delPermission') != -1"
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
            :current-page="query.pageIndex"
            :page-size="query.pageSize"
            :total="pageTotal"
            @current-change="handlePageChange"
        ></el-pagination>
      </div>
    </div>
    <!-- 编辑弹出框 -->
    <el-dialog title="编辑" :visible.sync="editVisible" width="30%">
      <el-form ref="form" :rules="rules" :model="form" label-width="80px">
        <el-form-item label="权限名称" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="父级名称" prop="parent_id">
          <el-select v-model="form.parent_id" placeholder="请选择">
            <el-option
              v-for="item in menuList"
              v-if="item.value != form.id"
              :key="item.value"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="权限路径">
          <el-input v-model="form.path"></el-input>
        </el-form-item>
        <el-form-item label="排序">
          <el-input type="number" v-model="form.sort_num"></el-input>
        </el-form-item>
        <el-form-item label="菜单图标">
          <el-input v-model="form.icon"></el-input>
        </el-form-item>
        <el-form-item label="类型" prop='type'>
          <el-select v-model="form.type" placeholder='请选择'>
            <el-option
              v-for="item in typeList"
              :key="item.value"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="saveEdit">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { permissionAdd, permissionList, permissionDel, permissionMenu } from '@/request/api'
export default {
  data() {
    return {
      permissions:[],
      query: {
        type: '',
        name: '',
        parent_id:'',
        pageIndex: 1,
        pageSize: 10
      },
      rules: {
        name: [
          {required: true, message: '权限名称不能为空'},
          {min: 1, max: 64, message: '长度在1到64个字符之间'}
        ],
        type: [
          {required: true, message: '类型不能为空'}
        ]
      },
      typeList:[
        {'label':'菜单','value':1},
        {'label':'操作','value':2},
      ],
      menuList:[],
      menuIdNameList:{},
      tableData: [],
      editVisible: false,
      pageTotal: 0,
      form: {},
      idx: -1,
      rangeTime:[]
    };
  },
  created() {
    this.getData();
    this.getMenu();
    this.permissions=JSON.parse(localStorage.getItem('permissions'))
  },
  methods: {
    // 获取 easy-mock 的模拟数据
    getData() {
      permissionList(this.query).then(res => {
        if (res.code == 200) {
          this.tableData=res.data.data
          this.pageTotal=res.data.total
        } else {
          this.$message.error(res.msg)
        }
      }).catch((error)=>{
        console.log('getDataError:',error)
      })
    },
    getMenu() {
      permissionMenu().then(res => {
        if (res.code == 200) {
          this.menuList = res.data
          console.log(this.menuList);
          var minl = {}
          res.data.forEach(function(val){
            minl[val.value]=val.label
          })
          this.menuIdNameList=minl
        } else {
          console.log('menulist:',res.msg)
        }
      }).catch((error) => {
        console.log('getMenuError:',error)
      })
    },
    getParentName(parentId) {
      return this.menuIdNameList[parentId];
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
      }).then(() => {
        permissionDel({id:row.id}).then(res => {
          this.$message.success('删除成功');
          this.tableData.splice(index, 1);
        }).catch((error) => {
          console.log('delerror:',errro)
        })
      })
    },
    // 编辑操作
    handleEdit(index, row) {
      this.idx = index;
      if (row.parent_id == 0) {
        row.parent_id=''
      }
      this.form = row;
      this.editVisible = true;
    },
    // 保存编辑
    saveEdit() {
      this.$delete(this.form,'create_time')
      permissionAdd(this.form).then(res => {
        if (res.code == 200) {
          this.editVisible = false;
          this.$message.success(`修改第 ${this.idx + 1} 行成功`);
          this.$set(this.tableData, this.idx, res.data);
        }
      })
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
  width: 100px;
}

.handle-input {
  width: 300px;
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
