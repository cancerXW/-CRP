var app = getApp();
Page({
  data: {
    userName: '',
    userImg: ''
  },

  onLoad: function (data) {
    this.setData({
      userName: app.globalData.userName,
      userImg: app.globalData.userImg
    })
  },
  signOut: function (e) {
    wx.clearStorage();
    wx.redirectTo({
      url: '../login/login',
      success: function (res) {
        // success
        wx.hideToast();
      },
      fail: function () {
        // fail
      },
      complete: function () {
        // complete
      }
    })
  },
  coursesNav: function () {
    if (app.globalData.nowT == null) {
      wx.showToast({
        title: '正在获取数据',
        icon: 'loading',
        duration: 60000
      });
      var no = app.globalData.userNo;
      var password = app.globalData.userPassword;
      wx.request({
        url: 'https://www.qhh170226.com/lzzy/lzzy.php',
        data: {
          act: 'getTimeTable',
          no: no,
          password: password
        },
        method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
        header: {
          "content-type": "application/x-www-form-urlencoded"
        }, // 设置请求的 header
        success: function (res) {
          var timeTableArr = [];
          for (var i = 0; i < res.data.termData.length; i++) {
            timeTableArr[i] = null;
          }
          timeTableArr[res.data.termData.length - 1] = res.data.timeTableData;
          app.setData('nowT', res.data.termData.length - 1);
          app.setData('nowW', res.data.nowW - 1);
          app.setData('nowD', res.data.nowD);
          app.setData('weekData', res.data.weekData);
          app.setData('termData', res.data.termData);
          app.setData('timeTableData', timeTableArr);
          wx.navigateTo({
            url: '../courses/courses',
            success: function (res) {
              // success
              wx.hideToast();
            },
            fail: function () {
              // fail
            },
            complete: function () {
              // complete
            }
          })
        },
        fail: function () {
          // fail
        },
        complete: function () {
          // complete
        }
      })
    } else {
      wx.navigateTo({
        url: '../courses/courses',
        success: function (res) {
          // success
          wx.hideToast();
        },
        fail: function () {
          // fail
        },
        complete: function () {
          // complete
        }
      })
    }

  }
})
