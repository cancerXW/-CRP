/**课程表 */
var dayArr = ['周一', '周二', '周三', '周四', '周五', '周六', '周日'];
var app = getApp();
var nowT = null;
var nowW = null;
var nowD = null;
var _self = null;
Page({
  data: {
    tremArr: "",
    weekArr: "",
    dayArr: dayArr,
    tremIndex: "",
    weekIndex: "",
    dayIndex: "",
    forenoon: '',
    noon: '',
    afternoon: '',
    night: '',
    allDay: '',
    other: '',
  },
  onLoad: function (options) {
    // 生命周期函数--监听页面加载
    nowT = app.globalData.nowT;
    nowW = app.globalData.nowW;
    nowD = app.globalData.nowD;
    _self = this;
    this.setData({
      tremArr: app.globalData.termData,
      weekArr: app.globalData.weekData,
      tremIndex: nowT,
      weekIndex: nowW,
      dayIndex: nowD,
      forenoon: app.globalData.timeTableData[nowT][nowW][nowD][0],
      noon: app.globalData.timeTableData[nowT][nowW][nowD][1],
      afternoon: app.globalData.timeTableData[nowT][nowW][nowD][2],
      night: app.globalData.timeTableData[nowT][nowW][nowD][3],
      allDay: app.globalData.timeTableData[nowT][nowW][nowD][4],
      other: app.globalData.timeTableData[nowT][nowW][nowD][5],
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

  },
  tremPickerChange: function (e) {
    nowT = e.detail.value;
    if (app.globalData.timeTableData[nowT] == null) {
      wx.showToast({
        title: '正在获取数据',
        icon: 'loading',
        duration: 60000
      });
      var no = app.globalData.userNo;
      var password = app.globalData.userPassword;
      wx.request({
        url: '服务器地址',
        data: {
          act: 'getTimeTable',
          term: nowT,
          no: no,
          password: password
        },
        method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
        header: {
          "content-type": "application/x-www-form-urlencoded"
        }, // 设置请求的 header
        success: function (res) {
          var timeTableArr = app.globalData.timeTableData;
          timeTableArr[nowT] = res.data.timeTableData;
          app.setData('timeTableData', timeTableArr);
          nowW = 0;
          nowD = 0;
          _self.setData({
            tremArr: app.globalData.termData,
            weekArr: app.globalData.weekData,
            tremIndex: nowT,
            weekIndex: nowW,
            dayIndex: nowD,
            forenoon: app.globalData.timeTableData[nowT][nowW][nowD][0],
            noon: app.globalData.timeTableData[nowT][nowW][nowD][1],
            afternoon: app.globalData.timeTableData[nowT][nowW][nowD][2],
            night: app.globalData.timeTableData[nowT][nowW][nowD][3],
            allDay: app.globalData.timeTableData[nowT][nowW][nowD][4],
            other: app.globalData.timeTableData[nowT][nowW][nowD][5],
          });

        },
        fail: function () {
          // fail
        },
        complete: function () {
          // complete
          wx.hideToast();
        }
      })


    } else {
      this.setData({
        tremArr: app.globalData.termData,
        weekArr: app.globalData.weekData,
        tremIndex: nowT,
        weekIndex: nowW,
        dayIndex: nowD,
        forenoon: app.globalData.timeTableData[nowT][nowW][nowD][0],
        noon: app.globalData.timeTableData[nowT][nowW][nowD][1],
        afternoon: app.globalData.timeTableData[nowT][nowW][nowD][2],
        night: app.globalData.timeTableData[nowT][nowW][nowD][3],
        allDay: app.globalData.timeTableData[nowT][nowW][nowD][4],
        other: app.globalData.timeTableData[nowT][nowW][nowD][5],
      });
    }
  },
  weekPickerChange: function (e) {
    console.log(app.globalData.timeTableData);
    nowW = e.detail.value;
    nowD = 0;
    this.setData({
      tremArr: app.globalData.termData,
      weekArr: app.globalData.weekData,
      tremIndex: nowT,
      weekIndex: nowW,
      dayIndex: nowD,
      forenoon: app.globalData.timeTableData[nowT][nowW][nowD][0],
      noon: app.globalData.timeTableData[nowT][nowW][nowD][1],
      afternoon: app.globalData.timeTableData[nowT][nowW][nowD][2],
      night: app.globalData.timeTableData[nowT][nowW][nowD][3],
      allDay: app.globalData.timeTableData[nowT][nowW][nowD][4],
      other: app.globalData.timeTableData[nowT][nowW][nowD][5],
    });
  },
  dayPickerChange: function (e) {
    nowD = e.detail.value;
    this.setData({
      tremArr: app.globalData.termData,
      weekArr: app.globalData.weekData,
      tremIndex: nowT,
      weekIndex: nowW,
      dayIndex: nowD,
      forenoon: app.globalData.timeTableData[nowT][nowW][nowD][0],
      noon: app.globalData.timeTableData[nowT][nowW][nowD][1],
      afternoon: app.globalData.timeTableData[nowT][nowW][nowD][2],
      night: app.globalData.timeTableData[nowT][nowW][nowD][3],
      allDay: app.globalData.timeTableData[nowT][nowW][nowD][4],
      other: app.globalData.timeTableData[nowT][nowW][nowD][5],
    });
  }

})
