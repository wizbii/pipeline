#!/bin/bash
set -ex

svcs=$(${BASE_PATH}/bin/console pipeline:frontend:list)
for svc in ${svcs} ;  do
  cat >/etc/systemd/system/$svc.service <<EOF

[Unit]
Description=%p

[Service]
ExecStart=${BASE_PATH}/bin/console rabbitmq:consumer -w $svc
Restart=always

[Install]
WantedBy=multi-user.target
EOF

  systemctl enable $svc
done

cat >/etc/systemd/system/pipeline_back@.service <<EOF
[Service]
ExecStart=${BASE_PATH}/bin/console rabbitmq:consumer -w pipeline_back
Restart=always

[Install]
WantedBy=multi-user.target
EOF

cat >/etc/systemd/system/pipeline_back_systemd_provisioner.service <<'EOF'
[Unit]
Description=Service that enables N instances of a service

[Service]
ExecStart=/bin/bash -c "for i in `seq $${BACK_INSTANCES_COUNT:-1}` ; do systemctl enable pipeline_back@$${i} ; systemctl start pipeline_back@$${i}; done"
RemainAfterExit=yes

[Install]
WantedBy=multi-user.target
EOF

systemctl enable pipeline_back_systemd_provisioner.service

