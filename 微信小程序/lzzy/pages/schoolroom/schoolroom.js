/**活动分 */
var app = getApp();
Page({
  data: {
    schoolroomTotalNumber: '',
    schoolroomTotalScore: '',
    schoolroomData: '',
  },
  onLoad: function (options) {
    // 页面初始化 options为页面跳转所带来的参数
    this.setData({
      schoolroomTotalNumber: app.globalData.schoolroomTotalNumber,
      schoolroomTotalScore: app.globalData.schoolroomTotalScore,
      schoolroomData: app.globalData.schoolroomData,
    });
  },
  onReady: function () {
    // 页面渲染完成
  },
  onShow: function () {
    // 页面显示
  },
  onHide: function () {
    // 页面隐藏
  },
  onUnload: function () {
    // 页面关闭
  }
})