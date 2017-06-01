/**学分 */
var app=getApp();
Page({
  data: {
    courseNumber: '',
    learnNumber: '',
    noPassNumber: '',
    learnData: '',


  },
  onLoad: function (options) {
    // 生命周期函数--监听页面加载
    this.setData({
      courseNumber: app.globalData.courseNumber,
      learnNumber: app.globalData.learnNumber,
      noPassNumber: app.globalData.noPassNumber,
      learnData: app.globalData.learnData,
    });

  },
  onReady: function () {
    // 生命周期函数--监听页面初次渲染完成

  },
  onShow: function () {
    // 生命周期函数--监听页面显示

  },
  onHide: function () {
    // 生命周期函数--监听页面隐藏

  },
  onUnload: function () {
    // 生命周期函数--监听页面卸载

  },
  onPullDownRefresh: function () {
    // 页面相关事件处理函数--监听用户下拉动作

  },
  onReachBottom: function () {
    // 页面上拉触底事件的处理函数

  },
  onShareAppMessage: function () {
    // 用户点击右上角分享

  }
})