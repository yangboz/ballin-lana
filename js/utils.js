VICA.ConfigHandler = {
  getValue : function(key){
    var env;
    switch( window.location.hostname ){
      case "localhost":
      case "127.0.0.1":
        env = 'Local';
        break;
      // case "http://15.185.109.31/":
      // case "15.185.109.31":
        // env = 'Dev';
        // break;
      case "http://173.201.131.157/":
      case "173.201.131.157":
        env = 'Dev';
        break;
      case "yourdomain.com":
        env = 'Staging';
        break;
      default:
        throw('Unknown environment: ' + window.location.hostname );
    }
    return VICA.Config[env][key];
  }
};

