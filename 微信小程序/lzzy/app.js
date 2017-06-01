//app.js
App({
  onLaunch: function () {
    //调用API从本地缓存中获取数据
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)
  },
  getUserInfo: function (cb) {
    var that = this
    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo)
    } else {
      //调用登录接口
      wx.login({
        success: function () {
          wx.getUserInfo({
            success: function (res) {
              that.globalData.userInfo = res.userInfo
              typeof cb == "function" && cb(that.globalData.userInfo)
            }
          })
        }
      })
    }
  },
  globalData: {
    userInfo: null,
    userNo: null,
    userPassword: null,
    userName: null,
    userImg: null,
    courseNumber: null,
    learnNumber: null,
    noPassNumber: null,
    learnData: null,
    teacherData: null,
    schoolroomTotalNumber: null,
    schoolroomTotalScore: null,
    schoolroomData: null,
    evaluateTotalScore: null,
    evaluateTotalNumber: null,
    evaluateData: null,
    achievementData: null,
    nowT: null,
    nowW: null,
    nowD: null,
    weekData: null,
    termData: null,
    timeTableData: null,
  },
  setData: function (t, d) {
    switch (t) {
      case "userNo":
        this.globalData.userNo = d;
        break;
      case "userPassword":
        this.globalData.userPassword = d;
        break;
      case "userName":
        this.globalData.userName = d;
        break;
      case "userImg":
        this.globalData.userImg = d;
        break;
      case "courseNumber":
        this.globalData.courseNumber = d;
        break;
      case "learnNumber":
        this.globalData.learnNumber = d;
        break;
      case "noPassNumber":
        this.globalData.noPassNumber = d;
        break;
      case "learnData":
        this.globalData.learnData = d;
        break;
      case "teacherData":
        this.globalData.teacherData = d;
        break;
      case "schoolroomTotalNumber":
        this.globalData.schoolroomTotalNumber = d;
        break;
      case "schoolroomTotalScore":
        this.globalData.schoolroomTotalScore = d;
        break;
      case "schoolroomData":
        this.globalData.schoolroomData = d;
        break;
      case "teacherData":
        this.globalData.teacherData = d;
        break;
      case "evaluateTotalScore":
        this.globalData.evaluateTotalScore = d;
        break;
      case "evaluateTotalNumber":
        this.globalData.evaluateTotalNumber = d;
        break;
      case "evaluateData":
        this.globalData.evaluateData = d;
        break;
      case "achievementData":
        this.globalData.achievementData = d;
        break;
      case "achievementData":
        this.globalData.achievementData = d;
        break;
      case "nowT":
        this.globalData.nowT = d;
        break;
      case "nowW":
        this.globalData.nowW = d;
        break;
      case "nowD":
        this.globalData.nowD = d;
        break;
      case "termData":
        this.globalData.termData = d;
        break;
      case "weekData":
        this.globalData.weekData = d;
        break;
      case "timeTableData":
        this.globalData.timeTableData = d;
        break;
      case "timeTableData":
        this.globalData.timeTableData = d;
        break;
    }

  },

})