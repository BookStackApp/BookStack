# BookStack 页面阅读进度功能指南

## 功能概述

本指南介绍如何在 BookStack 项目中使用新添加的页面阅读进度功能，包括实时进度条、自动保存、阅读统计和章节导航。

## 功能特性

### 1. 实时阅读进度条
- 显示当前页面的阅读百分比
- 自动计算滚动位置
- 支持手动标记已读

### 2. 自动保存和恢复
- 自动保存阅读位置
- 页面刷新后恢复上次阅读位置
- 支持键盘快捷键操作

### 3. 阅读统计
- 总阅读页面数统计
- 平均阅读时间计算
- 阅读完成率分析
- 连续阅读天数追踪

### 4. 章节导航和已读标记
- 章节列表显示
- 页面完成状态标记
- 进度可视化展示
- 快速导航到未读页面

## 项目启动步骤

### 1. 环境准备
确保已安装以下环境：
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/PostgreSQL

### 2. 安装依赖
```bash
# 安装 PHP 依赖
composer install

# 安装 Node.js 依赖
npm install

# 编译前端资源
npm run dev
```

### 3. 数据库设置
```bash
# 运行数据库迁移
php artisan migrate

# 运行测试数据填充（可选）
php artisan db:seed
```

### 4. 启动开发服务器
```bash
# 启动 Laravel 开发服务器
php artisan serve

# 启动前端热重载（新终端）
npm run hot
```

## 功能测试指南

### 1. 运行自动化测试

#### 后端测试
```bash
# 运行所有测试
php artisan test

# 仅运行阅读进度功能测试
php artisan test tests/Feature/ReadingProgressTest.php

# 运行单元测试
php artisan test tests/Unit/ReadingProgressComponentsTest.php
```

#### 前端测试
```bash
# 检查组件语法
npm run build

# 检查 TypeScript 错误
npm run type-check
```

### 2. 手动功能测试

#### 测试实时进度条
1. 登录系统
2. 打开任意书籍页面
3. 滚动页面查看进度条变化
4. 验证百分比显示准确性

#### 测试自动保存
1. 阅读页面并滚动到任意位置
2. 刷新页面
3. 验证是否恢复到上次阅读位置
4. 检查控制台网络请求

#### 测试阅读统计
1. 阅读多个页面
2. 访问个人资料页
3. 查看阅读统计数据
4. 验证数据准确性

#### 测试章节导航
1. 打开包含多个章节的图书
2. 使用章节导航组件
3. 检查已读/未读标记
4. 测试快速导航功能

### 3. API 接口测试

使用 Postman 或 curl 测试 API：

```bash
# 获取页面阅读进度
curl -X GET "http://localhost:8000/api/pages/1/reading-progress" \
  -H "Authorization: Bearer YOUR_TOKEN"

# 更新阅读进度
curl -X PUT "http://localhost:8000/api/pages/1/reading-progress" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"progress_percentage":75,"current_scroll_position":500,"time_spent_seconds":300}'

# 获取用户阅读统计
curl -X GET "http://localhost:8000/api/users/me/reading-stats" \
  -H "Authorization: Bearer YOUR_TOKEN"

# 获取用户所有阅读进度
curl -X GET "http://localhost:8000/api/users/me/reading-progress" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 文件结构说明

### 后端文件
- `app/Entities/Models/ReadingProgress.php` - 阅读进度模型
- `app/Entities/Controllers/ReadingProgressApiController.php` - API 控制器
- `database/migrations/2025_12_11_000000_create_reading_progress_table.php` - 数据库迁移
- `routes/api.php` - API 路由配置

### 前端文件
- `resources/js/components/reading-progress-bar.js` - 进度条组件
- `resources/js/components/page-reading-progress.js` - 页面阅读进度组件
- `resources/js/components/user-reading-stats.js` - 用户统计组件
- `resources/js/components/chapter-navigation.js` - 章节导航组件
- `resources/js/services/reading-progress.js` - 阅读进度服务
- `resources/sass/components/_reading-progress.scss` - 样式文件

### 测试文件
- `tests/Feature/ReadingProgressTest.php` - 功能测试
- `tests/Unit/ReadingProgressComponentsTest.php` - 组件测试

## 故障排除

### 常见问题

#### 1. 迁移失败
```bash
# 检查数据库连接
php artisan config:clear
php artisan migrate:status

# 重新运行迁移
php artisan migrate:fresh
```

#### 2. 前端编译错误
```bash
# 清除缓存
npm run clean

# 重新安装依赖
rm -rf node_modules package-lock.json
npm install
npm run dev
```

#### 3. API 404 错误
```bash
# 检查路由缓存
php artisan route:clear
php artisan route:list | grep reading-progress
```

#### 4. 权限问题
```bash
# 检查文件权限
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## 性能优化建议

### 1. 数据库优化
- 确保 `reading_progress` 表有适当的索引
- 定期清理旧的阅读进度数据
- 考虑使用缓存存储频繁查询的数据

### 2. 前端优化
- 使用懒加载减少初始加载时间
- 实现防抖机制减少 API 调用频率
- 使用 Web Workers 处理大量计算

### 3. 用户体验
- 添加加载状态指示器
- 实现平滑滚动动画
- 提供键盘快捷键支持

## 创建 Pull Request

### 1. 创建开发分支
```bash
git checkout development
git pull origin development
git checkout -b feature/reading-progress
```

### 2. 提交更改
```bash
git add .
git commit -m "feat: 添加页面阅读进度功能

- 实现实时阅读进度条
- 添加自动保存和恢复功能
- 创建用户阅读统计页面
- 实现章节导航和已读标记
- 添加完整的测试用例"
```

### 3. 推送并创建 PR
```bash
git push origin feature/reading-progress
```

然后在 GitHub/GitLab 上创建 Pull Request，目标分支选择 `development`。

### PR 模板

```markdown
## 功能描述
添加了页面阅读进度功能，提升用户阅读体验。

## 功能特性
- [x] 实时阅读进度条
- [x] 自动保存和恢复阅读位置
- [x] 用户阅读统计
- [x] 章节导航和已读标记

## 技术实现
- 使用 Vue.js 组件实现前端功能
- 创建 reading_progress 数据表
- 添加 RESTful API 端点
- 遵循项目现有代码规范

## 测试
- [x] 单元测试通过
- [x] 功能测试通过
- [x] 手动测试验证

## 文档
- [x] 添加了使用指南
- [x] 更新了 API 文档
```

## 后续改进建议

1. **社交功能**：添加阅读分享和评论功能
2. **个性化**：基于阅读历史推荐内容
3. **移动端**：优化移动端阅读体验
4. **离线模式**：支持离线阅读进度同步
5. **数据分析**：添加详细的阅读分析报告

## 联系支持

如有问题或建议，请通过以下方式联系：
- 创建 GitHub Issue
- 发送邮件到开发团队
- 在讨论区发帖

---

*最后更新：2025年1月11日*