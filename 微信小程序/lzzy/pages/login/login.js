/**登录逻辑处理 */

var app = getApp();
Page({
  data: {
    userNo: "",
    userPassword: ""
  },
  onLoad: function (options) {
    // 生命周期函数--监听页面加载
    //页面初始化完成后判断是否有登录记录直接自动登录
    var no = wx.getStorageSync('userNo');
    var password = wx.getStorageSync('userPassword');
    if (no != "" || password != "") {
      this.setData({
        userNo: no,
        userPassword: password
      });
      wx.showToast({
        title: '自动登录中',
        icon: 'loading',
        duration: 60000
      });
      //向服务器发送登录请求
      wx.request({
        url: '服务器地址',
        data: {
          act: 'login',
          no: no,
          password: password,
        },
        method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
        header: {
          "content-type": "application/x-www-form-urlencoded"//设置提交内容类型，以便服务器能获取
        }, // 设置请求的 header
        success: function (res) {
          //判断是否登录成功
          if (res.data.info != undefined) {
            wx.showToast({
              title: res.data.info,
              duration: 1500
            })
            return;
          }
          //登录成功缓存账号密码，一边下次自动登录
          wx.setStorageSync('userNo', no);
          wx.setStorageSync('userPassword', password);
          //存储学分、老师、活动分、诚信分、综合成绩
          app.setData('userNo', no);
          app.setData('userPassword', password);
          app.setData('userName', res.data.userName);
          app.setData('userImg', res.data.userImg);
          app.setData('courseNumber', res.data.courseNumber);
          app.setData('learnNumber', res.data.learnNumber);
          app.setData('noPassNumber', res.data.noPassNumber);
          app.setData('learnData', res.data.learnData);
          app.setData('teacherData', res.data.teacherData);
          app.setData('schoolroomTotalNumber', res.data.schoolroomTotalNumber);
          app.setData('schoolroomTotalScore', res.data.schoolroomTotalScore);
          app.setData('schoolroomData', res.data.schoolroomData);
          app.setData('evaluateTotalScore', res.data.evaluateTotalScore);
          app.setData('evaluateTotalNumber', res.data.evaluateTotalNumber);
          app.setData('evaluateData', res.data.evaluateData);
          app.setData('achievementData', res.data.achievementData);
          // success
          //跳转至首页
          wx.redirectTo({
            url: '../index/index',
            success: function (res) {
              // success

            },
            fail: function () {
              // fail
            },
            complete: function () {
              // complete
              wx.hideToast();
            }
          })

        },
        fail: function () {
          // fail
          wx.showToast({
            title: "服务器异常!",
            duration: 1500
          })
          return;
        },
        complete: function () {
          // complete
        }
      })
    }

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
  formSubmit: function (e) {
    //获取账号密码
    var no = e.detail.value.no;
    var password = e.detail.value.password;
    //数据判定
    if (no == "") {
      wx.showToast({
        title: '请填写学号',
        duration: 1500
      })
      return;
    }
    if (password == "") {
      wx.showToast({
        title: '请填写密码',
        duration: 1500
      })
      return;
    }

    wx.showToast({
      title: '登录中',
      icon: 'loading',
      duration: 60000
    });

    //向服务器发送登录请求
    wx.request({
      url: '服务器地址',
      data: {
        act: 'login',
        no: no,
        password: password,
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {
        "content-type": "application/x-www-form-urlencoded"//设置提交内容类型，以便服务器能获取
      }, // 设置请求的 header
      success: function (res) {
        if (res.data.info != undefined) {
          //判断是否登录成功
          wx.showToast({
            title: res.data.info,
            duration: 1500
          })
          return;
        }
        //登录成功缓存账号密码，一边下次自动登录
        wx.setStorageSync('userNo', no);
        wx.setStorageSync('userPassword', password);
        //存储学分、老师、活动分、诚信分、综合成绩
        app.setData('userNo', no);
        app.setData('userPassword', password);
        app.setData('userName', res.data.userName);
        app.setData('userImg', res.data.userImg);
        app.setData('courseNumber', res.data.courseNumber);
        app.setData('learnNumber', res.data.learnNumber);
        app.setData('noPassNumber', res.data.noPassNumber);
        app.setData('learnData', res.data.learnData);
        app.setData('teacherData', res.data.teacherData);
        app.setData('schoolroomTotalNumber', res.data.schoolroomTotalNumber);
        app.setData('schoolroomTotalScore', res.data.schoolroomTotalScore);
        app.setData('schoolroomData', res.data.schoolroomData);
        app.setData('evaluateTotalScore', res.data.evaluateTotalScore);
        app.setData('evaluateTotalNumber', res.data.evaluateTotalNumber);
        app.setData('evaluateData', res.data.evaluateData);
        app.setData('achievementData', res.data.achievementData);
        // success
        //跳转至首页
        wx.redirectTo({
          url: '../index/index',
          success: function (res) {
            // success

          },
          fail: function () {
            // fail
          },
          complete: function () {
            // complete
            wx.hideToast();
          }
        })

      },
      fail: function () {
        // fail
        wx.showToast({
          title: "服务器异常!",
          duration: 1500
        })
        return;
      },
      complete: function () {
        // complete
      }
    })
  }
})