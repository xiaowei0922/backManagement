<template>
  <div>
    <div class="crumbs">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>
          <i class="el-icon-lx-cascades"></i> 商品管理
        </el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="container">
      <div class="handle-box">
        <router-link to="/goodsAdd"><el-button
          type="primary"
          icon="el-icon-delete"
          class="handle-del mr10"
        >添加商品</el-button></router-link>
        <el-select v-model="query.address" placeholder="状态" class="handle-select mr10">
          <el-option key="1" label="上架" value="1"></el-option>
          <el-option key="2" label="下架" value="2"></el-option>
        </el-select>
        <el-input v-model="query.name" placeholder="商品名称" class="handle-input mr10"></el-input>
        <el-button type="primary" icon="el-icon-search" @click="handleSearch">搜索</el-button>
      </div>
      <el-table
        :data="tableData"
        border
        class="table"
        header-cell-class-name="table-header"
      >
        <el-table-column prop="id" label="ID" width="55" align="center"></el-table-column>
        <el-table-column prop="name" label="商品名称"></el-table-column>
        <el-table-column prop="price" label="商品价格"></el-table-column>
        <el-table-column prop="pic" label="商品图片"></el-table-column>
        <el-table-column prop="discription" label="商品简介"></el-table-column>
        <el-table-column label="状态" align="center">
          <template slot-scope="scope">
            <el-tag :type="scope.row.status==1?'success':'danger'">{{scope.row.status==1?'正常':'禁用'}}</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="添加时间"></el-table-column>
        <el-table-column prop="update_time" label="修改时间"></el-table-column>
        <el-table-column label="操作" width="180" align="center">
          <template slot-scope="scope">
            <router-link :to="{path:'roleAdd',query:{'id':scope.row.id}}">
              <el-button
                v-if="permissions.indexOf('/admin/roles/saveRole') != -1 && permissions.indexOf('/admin/roles/detail') != -1"
                type="text"
                icon="el-icon-edit"
              >编辑</el-button>
            </router-link>
            <el-button
              v-if="permissions.indexOf('/admin/roles/delRole') != -1"
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
  </div>
</template>

<script>
export default {
  name: 'basetable',
  data() {
    return {
      permissions:[],
      query: {
        address: '',
        name: '',
        pageIndex: 1,
        pageSize: 10
      },
      tableData: [],
      multipleSelection: [],
      delList: [],
      editVisible: false,
      pageTotal: 0,
      form: {},
      idx: -1,
      id: -1
    };
  },
  created() {
    this.getData();
    this.permissions=JSON.parse(localStorage.getItem('permissions'))
  },
  methods: {
    // 获取 easy-mock 的模拟数据
    getData() {

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
        this.$message.success('删除成功');
        this.tableData.splice(index, 1);
      })
      .catch(() => {});
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
</style>
