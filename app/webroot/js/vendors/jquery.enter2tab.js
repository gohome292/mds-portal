$.extend($.fn,{
	enter2tab:function(o){
		var defaults={
			Enter: true,
			Tab: true
		}
		var o=$.extend(defaults, o);
		var Focus_Move = function(df,i,ln,shift){
			//フォーカスを取得できないものは飛ばします
			var mv;
			if (shift){
				mv	= -1;
			}else{
				mv	= 1;
			}
			var j = (i+mv) % ln;
			var Fo,Fs;
			while(i=j){
				Fo = df[j];
				Fs = Fo.style;
				if	(Fo.type!='hidden' &&
					!Fo.disabled &&
					Fo.tabIndex!=-1 &&
					Fs.visibility!='hidden' &&
					Fs.display!='none'){
					//対象のオブジェクトを戻す
					return Fo;
				}
				j=(j+mv) % ln;
			}
			//Hitしない場合
			return df[i];
		}
		var $input = $(this)
		$input.keydown(function(e){
			var k = e.keyCode;
			var s = e.shiftKey;
			var obj = e.target;
			var blKey = true;
			if (!o.Enter && k==13) return true;
			if (!o.Tab && k==9) return true;

			if (k == 13 ||k == 9){
			//Enter tabによるフォーカス移動
				switch(obj.tagName){
				case 'TEXTAREA':
					if (k!=13) blKey = false;
					break;
				case 'INPUT':
				case 'SELECT':
					//fileは巧く動かないので除外
					if (obj.type!='file') blKey = false;
					break;
				default:
				}
				//keyイベントを処理するもののみ抽出
				if (!blKey){
					//フォームオブジェクトが何番目か探す
					var ln = $input.length;
					var i;
					for (i=0; i<ln; i++){
						if ($input[i]==obj) break;
					}
					//次のフォームオブジェクト探す
					obj = Focus_Move($input,i,ln,s);
				}
			}

			if (!blKey){
				//イベントを伝播しない
				if (obj.type!='file'){
					//IE規定の動作キャンセル
					if(document.all) window.event.keyCode = 0;
					obj.focus();
					if (obj.select && obj.type!='button') obj.select();
				}else{
					blKey = true;
				}
			}
			return blKey;
		});
	}
});
